<?php

namespace App\Models;

use App\Builders\CaseCategoryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** What kind of need an assistance covers (food, housing, medical...). `name` is {en, ar}. */
class CaseCategory extends SiteModelSoftDelete
{
    use HasFactory;

    protected $table = 'case_categories';

    protected $fillable = ['name', 'order_id', 'active'];

    protected $searchable = ['name'];

    public function assistances(): HasMany
    {
        return $this->hasMany(CaseAssistance::class, 'category_id');
    }

    /** A category still holding assistances cannot be deleted: they would lose their category. */
    public function isInUse(): bool
    {
        return $this->assistances()->exists();
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

    public function newEloquentBuilder($query): CaseCategoryBuilder
    {
        return new CaseCategoryBuilder($query);
    }
}
