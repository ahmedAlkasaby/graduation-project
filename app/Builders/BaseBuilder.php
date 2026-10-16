<?php

namespace App\Builders;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;


class BaseBuilder extends Builder
{
    public function active(): static
    {
        return $this->where('active', 1);
    }

    public function defaultOrder(
        string $order_by = 'order_id',
        string $order_type = 'ASC',
        string $order_created_type = 'DESC',
        string $order_created = 'created_at',
    ): static {
        return $this->orderBy($order_by, $order_type)
            ->orderBy($order_created, $order_created_type);
    }

    public function ofType(string $type): static
    {
        return $this->where('type', $type);
    }

    public function ofTypeArray(array $type): static
    {
        return $this->whereIn('type', $type);
    }

    public function visibleTo(string $type_app = 'app', $request = null): static
    {
        if ($type_app === 'app') {
            return $this->where('active', 1);
        }

        $active = ($request ?? request())->input('active');

        if (! is_null($active) && (int) $active !== -1) {
            $this->where('active', (int) $active);
        }

        return $this;
    }

    public function applyOrder($request = null, ?array $allowed = null): static
    {
        $request = $request ?? request();
        $allowed = $allowed ?? $this->getModel()->getOrderByAllowed();

        $order_by = $request->input('order_by', 'order_id');
        $order_by = in_array($order_by, $allowed) ? $order_by : 'order_id';

        $order_type = strtoupper($request->input('order_type', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        return $this->defaultOrder($order_by, $order_type);
    }
    protected function searchTerm($request): mixed
    {
        return $request->input('name') ?? $request->input('search');
    }


    public function mainSearch(mixed $search): static
    {
        if (! $search) {
            return $this;
        }

        $terms = array_values(array_filter(array_map('trim', array_map('strval', (array) $search)), fn ($v) => $v !== ''));

        if ($terms === []) {
            return $this;
        }

        $searchable = $this->getModel()->getSearchable();

        if ($searchable === []) {
            return $this;
        }

        if ($this->canUseFullText($terms)) {
            return $this->whereFullText(
                $this->getModel()->getFullTextSearchable(),
                implode(' ', array_map(fn ($t) => '+'.$t.'*', $terms)),
                ['mode' => 'boolean'],
            );
        }

        return $this->where(function (Builder $query) use ($terms, $searchable) {
            foreach ($searchable as $column) {
                foreach ($terms as $value) {
                    $query->orWhere($column, 'like', "%{$value}%");
                }
            }
        });
    }


    protected const FULL_TEXT_MIN_TERM = 3;

    protected function canUseFullText(array $terms): bool
    {
        if ($this->getModel()->getFullTextSearchable() === []) {
            return false;
        }

        if (! in_array($this->getConnection()->getDriverName(), ['mysql', 'mariadb', 'pgsql'], true)) {
            return false;
        }

        foreach ($terms as $term) {
            if (mb_strlen($term) < self::FULL_TEXT_MIN_TERM) {
                return false;
            }
        }

        return true;
    }

    public function mainApplyDynamicFilters(array $filters = []): static
    {
        foreach ($filters as $column => $value) {
            if (! is_null($value) && (int) $value !== -1) {
                $this->where($column, $value);
            }
        }

        return $this;
    }


    public function filterByRelation(
        string $relation,
        string $column,
        mixed $values,
        ?callable $callback = null,
    ): static {
        if (empty($values) || $values === 'all' || $values === -1 || $values === '-1') {
            return $this;
        }

        foreach ((array) $values as $value) {
            $this->whereHas($relation, function (Builder $query) use ($value, $column, $callback) {
                $callback ? $callback($query, [$value]) : $query->where($column, $value);
            });
        }

        return $this;
    }


    public function trash($request = null): static
    {
        $request = $request ?? request();

        if ($request->filled('trash') && $request->input('trash') === 'all') {
            return $this->withTrashed();
        }

        if ($request->input('trash') === '0') {
            return $this->onlyTrashed();
        }

        return $this->withoutTrashed();
    }


    public function limitFor($request = null, ?int $default = null): int
    {
        $default = $default ?: (int) (settings()->get('table_limit') ?: 20);

        $requested = (int) ($request ?? request())->input('limit', 0);

        return $requested > 0 ? $requested : max(1, $default);
    }



    public function paginateRequested($request = null, ?int $default = null, bool $allowAll = false): LengthAwarePaginator
    {
        $request = $request ?? request();

        return $allowAll && $request->has('limit') && (int) $request->input('limit') === 0
            ? $this->paginate(max(1, $this->count()))
            : $this->paginate($this->limitFor($request, $default));
    }

    public function dateFilter($request, string $column = 'created_at'): static
    {
        if ($request->filled('date_start')) {
            $this->whereDate($column, '>=', $request->input('date_start'));
        }

        if ($request->filled('date_end')) {
            $this->whereDate($column, '<=', $request->input('date_end'));
        }

        return $this;
    }
}
