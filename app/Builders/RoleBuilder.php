<?php

namespace App\Builders;

class RoleBuilder extends BaseBuilder
{
    /**
     * Roles have no `active` flag and no `order_id`, so the list filters on the
     * search box alone and stays newest first.
     *
     * @param  array{search?: string}  $filters
     */
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)->latest();
    }
}
