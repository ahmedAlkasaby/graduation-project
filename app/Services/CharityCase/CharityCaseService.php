<?php

namespace App\Services\CharityCase;

use App\Enums\UserTypeEnum;
use App\Models\CaseCategory;
use App\Models\CharityCase;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Beneficiary profiles: saving them, the select options their form needs,
 * and the searchable beneficiary select of the assistance form.
 */
class CharityCaseService
{
    private const LOOKUP_LIMIT = 20;

    /**
     * @param  array{full_name: string, national_id: string, phone: string, category_id: int, volunteer_id: int}  $data
     */
    public function create(array $data): CharityCase
    {
        return CharityCase::create($data);
    }

    /**
     * @param  array{full_name: string, national_id: string, phone: string, category_id: int, volunteer_id: int}  $data
     */
    public function update(CharityCase $charityCase, array $data): CharityCase
    {
        $charityCase->update($data);

        return $charityCase;
    }

    /**
     * Beneficiaries matching a name, national ID or phone, for the searchable
     * select. An empty term returns the latest ones.
     *
     * @return Collection<int, CharityCase>
     */
    public function lookup(?string $term): Collection
    {
        return CharityCase::query()
            ->search($term)
            ->latest('id')
            ->limit(self::LOOKUP_LIMIT)
            ->get(['id', 'full_name', 'national_id', 'phone']);
    }

    /**
     * What the profile form's selects offer: the active categories and the
     * active volunteers.
     *
     * @return array{categories: array<int, string>, volunteers: array<int, string>}
     */
    public function formOptions(): array
    {
        $volunteers = User::query()
            ->ofType(UserTypeEnum::Volunteer->value)
            ->where('active', true)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'phone'])
            ->mapWithKeys(fn (User $user): array => [$user->id => trim($user->full_name.' '.$user->phone)])
            ->all();

        return [
            'categories' => CaseCategory::options(),
            'volunteers' => $volunteers,
        ];
    }
}
