<?php

namespace App\Builders;

class RoleBuilder extends BaseBuilder
{
    /**
     * Roles have no `active` flag and no `order_id`, so the list filters on the
     * search box alone and stays newest-first.
     */
    public function filter($request = null): static
    {
        $request = $request ?? request();

        return $this->mainSearch($this->searchTerm($request))->latest();
    }
}
