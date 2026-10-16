<?php

namespace App\Builders;

class UserBuilder extends BaseBuilder
{
    /**
     * Narrows the admin list to one kind of user (super_admin, admin,
     * sub_admin). No value — or 'all' — leaves every user in.
     */
    public function userType($request = null, string $userType = 'all'): static
    {
        $type = $request?->input('type') ?: $userType;

        return $type === 'all' ? $this : $this->where('type', $type);
    }

    public function ofRole(string $role): static
    {
        return $this->whereHas('roles', fn ($q) => $q->where('name', $role));
    }

    public function filter($request = null): static
    {
        $request = $request ?? request();

        return $this->mainSearch($request->only(['name', 'email', 'phone']))
            ->userType($request)
            ->mainApplyDynamicFilters($request->only(['country_id', 'city_id', 'active']))
            ->dateFilter($request);
    }
}
