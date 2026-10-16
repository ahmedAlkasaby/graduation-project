<?php

namespace App\Builders;

class CityBuilder extends BaseBuilder
{
    public function filter($request = null, string $type_app = 'app'): static
    {
        $request = $request ?? request();

        return $this->visibleTo($type_app, $request)
            ->mainSearch($this->searchTerm($request))
            ->mainApplyDynamicFilters($request->only(['country_id']))
            ->defaultOrder();
    }
}
