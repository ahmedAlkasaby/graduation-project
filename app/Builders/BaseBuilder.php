<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Query methods shared by every model. They take plain values, never the
 * request: the controller validates the input and passes it in.
 */
class BaseBuilder extends Builder
{
    public function active(bool $active = true): static
    {
        return $this->where('active', $active);
    }

    /** `order_id` first, then newest first. */
    public function defaultOrder(): static
    {
        return $this->orderBy('order_id')->latest();
    }

    /** Looks for the term in the model's searchable columns. */
    public function search(?string $term): static
    {
        $columns = $this->getModel()->getSearchable();

        if (blank($term) || $columns === []) {
            return $this;
        }

        return $this->where(function (Builder $query) use ($columns, $term): void {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', "%{$term}%");
            }
        });
    }

    /** "with" adds deleted rows, "only" returns deleted rows alone. Soft deleting models only. */
    public function trashed(?string $trashed): static
    {
        return match ($trashed) {
            'with' => $this->withTrashed(),
            'only' => $this->onlyTrashed(),
            default => $this,
        };
    }

    public function createdBetween(?string $from, ?string $to, string $column = 'created_at'): static
    {
        return $this->when($from, fn (Builder $query) => $query->whereDate($column, '>=', $from))
            ->when($to, fn (Builder $query) => $query->whereDate($column, '<=', $to));
    }
}
