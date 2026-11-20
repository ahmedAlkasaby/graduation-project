<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserTypeEnum;
use App\Models\City;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Services\CharityCase\CharityStatisticsService;
use Illuminate\Contracts\View\View;

/** The landing page every signed in admin may open, and the "no access" page. */
class DashboardController extends AdminController
{
    /**
     * Every admin sees the user and location totals; the charity case and
     * assistance totals need the `statistics` permission.
     */
    public function index(CharityStatisticsService $statistics): View
    {
        $this->setClass('home');

        $usersByType = User::query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $cards = [
            ['label' => __('Donors'), 'value' => (int) ($usersByType[UserTypeEnum::Donor->value] ?? 0), 'icon' => 'heart-handshake', 'color' => 'danger'],
            ['label' => __('Volunteers'), 'value' => (int) ($usersByType[UserTypeEnum::Volunteer->value] ?? 0), 'icon' => 'users', 'color' => 'success'],
            ['label' => __('Admins'), 'value' => (int) ($usersByType[UserTypeEnum::Admin->value] ?? 0) + (int) ($usersByType[UserTypeEnum::SuperAdmin->value] ?? 0), 'icon' => 'user-shield', 'color' => 'primary'],
            ['label' => __('Cities'), 'value' => City::count(), 'icon' => 'map-pin', 'color' => 'info'],
            ['label' => __('Regions'), 'value' => Region::count(), 'icon' => 'map-2', 'color' => 'warning'],
            ['label' => __('Roles'), 'value' => Role::count(), 'icon' => 'lock', 'color' => 'secondary'],
        ];

        $sections = [['title' => null, 'cards' => $cards]];

        if ($this->can('statistics')) {
            $sections[] = ['title' => __('Shared Cases'), 'cards' => $this->caseCards($statistics->cases())];
            $sections[] = ['title' => __('Assistances'), 'cards' => $this->assistanceCards($statistics->assistances())];
        }

        return view('admin.dashboard.index', [
            'sections' => $sections,
            'currency' => (string) settings()->get('currency', 'EGP'),
        ]);
    }

    /**
     * @param  array{total: int, with_assistances: int, without_assistances: int}  $cases
     * @return list<array{label: string, value: int|float, icon: string, color: string, money?: bool}>
     */
    private function caseCards(array $cases): array
    {
        return [
            ['label' => __('Total Cases'), 'value' => $cases['total'], 'icon' => 'folders', 'color' => 'primary'],
            ['label' => __('Cases With Assistances'), 'value' => $cases['with_assistances'], 'icon' => 'heart-handshake', 'color' => 'success'],
            ['label' => __('Cases Without Assistances'), 'value' => $cases['without_assistances'], 'icon' => 'folder-question', 'color' => 'warning'],
        ];
    }

    /**
     * @param  array{total: int, active: int, one_time: int, recurring: int, paid_amount: float, remaining_amount: float, overdue_installments: int}  $assistances
     * @return list<array{label: string, value: int|float, icon: string, color: string, money?: bool}>
     */
    private function assistanceCards(array $assistances): array
    {
        return [
            ['label' => __('Total Assistances'), 'value' => $assistances['total'], 'icon' => 'gift', 'color' => 'primary'],
            ['label' => __('Active Assistances'), 'value' => $assistances['active'], 'icon' => 'circle-check', 'color' => 'success'],
            ['label' => __('One-time'), 'value' => $assistances['one_time'], 'icon' => 'cash', 'color' => 'info'],
            ['label' => __('Monthly'), 'value' => $assistances['recurring'], 'icon' => 'calendar-repeat', 'color' => 'secondary'],
            ['label' => __('Paid Total'), 'value' => $assistances['paid_amount'], 'icon' => 'coins', 'color' => 'success', 'money' => true],
            ['label' => __('Remaining Amount'), 'value' => $assistances['remaining_amount'], 'icon' => 'hourglass', 'color' => 'warning', 'money' => true],
            ['label' => __('Overdue Installments'), 'value' => $assistances['overdue_installments'], 'icon' => 'alert-triangle', 'color' => 'danger'],
        ];
    }

    public function unauthorized(): View
    {
        return view('admin.errors.unauthorized');
    }
}
