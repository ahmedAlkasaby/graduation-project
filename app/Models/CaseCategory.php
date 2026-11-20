<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** What kind of need a beneficiary has (food, housing, medical...). `name` is {en, ar}. */
class CaseCategory extends SiteModelSoftDelete
{
    use HasFactory;

    protected $table = 'case_categories';

    protected $fillable = ['name', 'order_id', 'active'];

    protected $searchable = ['name'];

    public function cases(): HasMany
    {
        return $this->hasMany(CharityCase::class, 'category_id');
    }

    /**
     * The active categories as `id => name` for a select, in `order_id` order.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        return static::listForSelect(null, 'id', 'nameLang', ['active'], ['id', 'name'], static::query()->orderBy('order_id'));
    }
}
