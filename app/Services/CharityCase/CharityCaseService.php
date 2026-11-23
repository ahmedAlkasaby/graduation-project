<?php

namespace App\Services\CharityCase;

use App\Enums\UserTypeEnum;
use App\Models\CharityCase;
use App\Models\City;
use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Beneficiary profiles: saving them (with their optional address), the select
 * options their form needs, and the searchable beneficiary select of the
 * assistance form.
 */
class CharityCaseService
{
    private const LOOKUP_LIMIT = 20;

    private const ADDRESS_FIELDS = ['city_id', 'region_id', 'address', 'latitude', 'longitude'];

    /**
     * `volunteer_id` and `address` are optional. The address needs at least its `city_id`.
     *
     * @param  array{full_name: string, national_id: string, phone: string, volunteer_id?: int|null, address?: array<string, mixed>|null}  $data
     */
    public function create(array $data): CharityCase
    {
        return DB::transaction(function () use ($data): CharityCase {
            $charityCase = CharityCase::create(Arr::except($data, 'address'));

            $this->saveAddress($charityCase, $data['address'] ?? null);

            return $charityCase;
        });
    }

    /**
     * An `address` left out of $data keeps the saved one. An empty `address` removes it.
     *
     * @param  array{full_name: string, national_id: string, phone: string, volunteer_id?: int|null, address?: array<string, mixed>|null}  $data
     */
    public function update(CharityCase $charityCase, array $data): CharityCase
    {
        return DB::transaction(function () use ($charityCase, $data): CharityCase {
            $charityCase->update(Arr::except($data, 'address'));

            if (array_key_exists('address', $data)) {
                $this->saveAddress($charityCase, $data['address']);
            }

            return $charityCase;
        });
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
     * What the profile form's selects offer: the active volunteers.
     *
     * @return array{volunteers: array<int, string>}
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

        return ['volunteers' => $volunteers];
    }

    /**
     * What the address part of the Blade form offers: the active cities, and
     * the active regions of each city, in display order, so the region select
     * can follow the city.
     *
     * @return array{cities: array<int, string>, regionsByCity: array<int, list<array{id: int, name: string|null}>>}
     */
    public function addressOptions(): array
    {
        $regionsByCity = Region::query()
            ->active()
            ->defaultOrder()
            ->get(['id', 'name', 'city_id'])
            ->groupBy('city_id')
            ->map(fn (Collection $regions): array => $regions
                ->map(fn (Region $region): array => ['id' => $region->id, 'name' => $region->nameLang()])
                ->all())
            ->all();

        return [
            'cities' => City::listForSelect(queryBuilder: City::query()->defaultOrder()),
            'regionsByCity' => $regionsByCity,
        ];
    }

    /**
     * Keeps one address per beneficiary: a city makes or replaces it, no city removes it.
     *
     * @param  array<string, mixed>|null  $address
     */
    private function saveAddress(CharityCase $charityCase, ?array $address): void
    {
        $values = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            array_merge(array_fill_keys(self::ADDRESS_FIELDS, null), Arr::only($address ?? [], self::ADDRESS_FIELDS)),
        );

        if ($values['city_id'] === null) {
            $charityCase->address()->delete();

            return;
        }

        $charityCase->address()->updateOrCreate([], $values);
    }
}
