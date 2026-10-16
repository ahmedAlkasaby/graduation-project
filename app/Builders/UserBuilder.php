<?php

namespace App\Builders;

class UserBuilder extends BaseBuilder
{
    /**
     * Narrows a list to one kind of user (donor, volunteer, admin). No value —
     * or 'all' — leaves every user in.
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
            ->dateFilter($request);
    }
    public function active(): static
    {
        return $this->where('active', true);
    }
}
