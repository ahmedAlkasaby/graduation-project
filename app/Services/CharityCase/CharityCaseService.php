<?php

namespace App\Services\CharityCase;

use App\Enums\UserTypeEnum;
use App\Models\CaseCategory;
use App\Models\CharityCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Beneficiary profiles: the select options their form needs, the searchable
 * beneficiary select of the assistance form, and the duplicate warnings shown
 * while a profile is typed in.
 *
 * Only the national ID is a hard duplicate (unique index + validation). A
 * shared phone or a similar name is a warning: family members share phones,
 * and different people share names.
 */
class CharityCaseService
{
    private const LOOKUP_LIMIT = 20;

    private const DUPLICATE_LIMIT = 5;

    /**
     * @param  array{full_name: string, national_id?: string|null, phone?: string|null, category_id: int, volunteer_id?: int|null}  $data
     */
    public function create(array $data): CharityCase
    {
        return CharityCase::create($data);
    }

    /**
     * @param  array{full_name: string, national_id?: string|null, phone?: string|null, category_id: int, volunteer_id?: int|null}  $data
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
     * Existing beneficiaries the typed-in profile may duplicate.
     *
     * @param  array{national_id?: string|null, phone?: string|null, full_name?: string|null}  $input
     * @return array{national_id: Collection<int, CharityCase>, phone: Collection<int, CharityCase>, similar_names: Collection<int, CharityCase>}
     */
    public function duplicates(array $input, ?int $ignoreId = null): array
    {
        $query = fn (): Builder => CharityCase::query()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->latest('id')
            ->limit(self::DUPLICATE_LIMIT);

        $nationalId = $input['national_id'] ?? null;
        $phone = $input['phone'] ?? null;
        $words = $this->nameWords($input['full_name'] ?? null);
        $columns = ['id', 'full_name', 'national_id', 'phone'];

        return [
            'national_id' => filled($nationalId) ? $query()->where('national_id', $nationalId)->get($columns) : collect(),
            'phone' => filled($phone) ? $query()->where('phone', $phone)->get($columns) : collect(),
            'similar_names' => $words !== []
                ? $query()->where(function (Builder $query) use ($words): void {
                    foreach ($words as $word) {
                        $query->where('full_name', 'like', "%{$word}%");
                    }
                })->get($columns)
                : collect(),
        ];
    }

    /**
     * What the profile form's selects offer. The case's current volunteer
     * stays listed even after being deactivated, so editing never drops them.
     *
     * @return array{categories: array<int, string>, volunteers: array<int, string>}
     */
    public function formOptions(?int $currentVolunteerId = null): array
    {
        $volunteers = User::query()
            ->ofType(UserTypeEnum::Volunteer->value)
            ->where(fn (Builder $query) => $query->where('active', true)
                ->when($currentVolunteerId, fn (Builder $query) => $query->orWhere('id', $currentVolunteerId)))
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'phone'])
            ->mapWithKeys(fn (User $user): array => [$user->id => trim($user->full_name.' '.$user->phone)])
            ->all();

        return [
            'categories' => CaseCategory::options(),
            'volunteers' => $volunteers,
        ];
    }

    /**
     * The first two words of a name that are long enough to compare, so
     * "Mohamed Ahmed" also finds "Mohamed Ahmed Ali".
     *
     * @return list<string>
     */
    private function nameWords(?string $name): array
    {
        return collect(preg_split('/\s+/u', trim((string) $name)) ?: [])
            ->filter(fn (string $word): bool => Str::length($word) >= 3)
            ->take(2)
            ->values()
            ->all();
    }
}
