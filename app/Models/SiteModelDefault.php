<?php

namespace App\Models;

use App\Builders\BaseBuilder;
use App\Services\ModelChangeLogger;
use App\Traits\AdminHelperTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Base model for the template's own tables.
 *
 * It holds what every table here genuinely shares — the translated-JSON casts
 * and their readers, the slug hook, and the hooks a builder needs. It is
 * deliberately not a library of static row helpers: those were inherited by
 * every model whether or not the table had the columns they addressed, and
 * each spent a select before its update. Query verbs belong on a builder, and
 * writes belong in an action or the model that owns them.
 */
class SiteModelDefault extends Model
{
    use AdminHelperTrait;

    protected $casts = [
        'name' => \App\Casts\UnescapedJson::class,
        'title' => \App\Casts\UnescapedJson::class,
        'content' => \App\Casts\UnescapedJson::class,
    ];

    /**
     * Query methods live on a builder rather than as scopes on the model.
     * Models with their own query language override this with a subclass.
     */
    public function newEloquentBuilder($query): BaseBuilder
    {
        return new BaseBuilder($query);
    }

    /** Columns mainSearch() looks through. */
    public function getSearchable(): array
    {
        return $this->searchable ?? [];
    }

    /** Columns the admin list is allowed to sort by. */
    public function getOrderByAllowed(): array
    {
        return $this->orderByAllowed ?? [];
    }


    public function getFullTextSearchable(): array
    {
        return $this->fullTextSearchable ?? [];
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            if (array_key_exists('link', $model->getAttributes()) || in_array('link', $model->getFillable())) {

                if (empty($model->link)) {

                    $name = $model->nameLang('en');


                    $slug = $name ? Str::slug($name) : Str::slug(Str::random(8));
                    $original = $slug;
                    $count = 1;

                    while (DB::table($model->getTable())->where('link', $slug)->exists()) {
                        $slug = $original . '-' . $count++;
                    }

                    $model->link = $slug;
                }
            }
        });

        /*
        | Change logging. Every table that extends this model is audited into
        | `logs` without asking, which is the point: a module cannot forget to
        | opt in. ModelChangeLogger decides what is worth a row — it skips the
        | log tables themselves, and the columns in $log_not_in.
        */
        static::created(fn ($model) => app(ModelChangeLogger::class)->created($model));
        static::updated(fn ($model) => app(ModelChangeLogger::class)->updated($model));
        static::deleted(fn ($model) => app(ModelChangeLogger::class)->deleted($model));
    }
    public static function listForSelect(
        $type = null,
        $key = 'id',
        $valueMethod = 'nameLang',
        $queryScope = ['active'],
        $columns = ['id', 'name'],
        $queryBuilder = null,
        $attribute_id = null
    ) {
        $query = $queryBuilder ?? static::query();

        // scopes are named by string here, so look them up on the builder
        foreach ((array) $queryScope as $scope) {
            if (method_exists($query, $scope)) {
                $query = $query->{$scope}();
            }
        }
        if ($attribute_id) {
            $query->where('attribute_id', $attribute_id);
        }

        $query->select($columns);

        $items = $query->get()->mapWithKeys(function ($item) use ($key, $valueMethod) {
            return [$item->$key => $item->$valueMethod()];
        })->toArray();

        if ($type === 'default') {
            $items = defaultOption() + $items;
        } elseif ($type === 'filter') {
            $items = filterOption() + $items;
        }

        return $items;
    }

    public function nameLang($lang = null)
    {
        $data = $this->name;
        if ($lang === null) {

            $defaultLang = app()->getLocale();
            return $data[$defaultLang] ?? null;
        }
        return $data[$lang] ?? null;
    }

    public function contentLang($lang = null)
    {
        $data = $this->content;
        if ($lang === null) {

            $defaultLang = app()->getLocale();
            return  $data[$defaultLang] ?? null;
        }
        return $data[$lang] ?? null;
    }

    public function titleLang($lang = null)
    {
        $data = $this->title;
        if ($lang === null) {

            $defaultLang = app()->getLocale();
            return  $data[$defaultLang] ?? null;
        }
        return $data[$lang] ?? null;
    }
    protected function asJson($value, $flags = 0)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }


    /**
     * Flip a boolean column on one row.
     *
     * One UPDATE, not findOrFail() + save(): the row is not needed, only the
     * write. Returns whether a row was actually touched, so a caller can tell
     * a missing id apart from a successful no-op.
     */
    public static function updateActive($id, $active, string $column = 'active'): bool
    {
        return static::query()->whereKey($id)->update([$column => $active]) > 0;
    }
}
