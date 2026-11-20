<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** What kind of need a beneficiary has (food, housing, medical...). `name` is {en, ar}. */
class CaseCategory extends SiteModel
{
    use HasFactory;

    protected $table = 'case_categories';

    protected $fillable = ['name'];

    protected $searchable = ['name'];

    public function cases(): HasMany
    {
        return $this->hasMany(CharityCase::class, 'category_id');
    }

    /**
     * Every category as `id => name` for a select. Categories have no active
     * flag, so the default "active" scope of listForSelect() is skipped.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        return static::listForSelect(null, 'id', 'nameLang', [], ['id', 'name'], static::query()->orderBy('id'));
    }
}
