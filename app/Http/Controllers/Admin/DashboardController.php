<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserTypeEnum;
use App\Models\City;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;

/** The landing page every signed in admin may open, and the "no access" page. */
class DashboardController extends AdminController
{
    public function index(): View
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

        return view('admin.dashboard.index', ['cards' => $cards]);
    }

    public function unauthorized(): View
    {
        return view('admin.errors.unauthorized');
    }
}
