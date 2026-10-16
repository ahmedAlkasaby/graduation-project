<?php

namespace App\Traits;

use App\Models\City;
use App\Models\Country;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;


trait AdminTrait
{
    use AdminHelperTrait;

    public function getCountries($all = false, $req = false): array
    {
        return $this->withNoneOption(Country::listForSelect(), $all, $req);
    }

    public function getCities($all = false, $req = false): array
    {
        return $this->withNoneOption(City::listForSelect(), $all, $req);
    }

    public function getUsers($all = true, $req = false): array
    {
        return $this->withNoneOption(
            User::active()
                ->select('id', 'full_name', 'phone')
                ->get()
                ->mapWithKeys(fn ($u) => [$u->id => trim($u->full_name.' - '.$u->phone)])
                ->toArray(),
            $all,
            $req
        );
    }

    public function getAllUsers($all = true, $req = false): array
    {
        return $this->withNoneOption(
            User::withTrashed()
                ->latest()
                ->select('id', 'full_name', 'phone')
                ->get()
                ->mapWithKeys(fn ($u) => [$u->id => trim($u->full_name.' - '.$u->phone)])
                ->toArray(),
            $all,
            $req
        );
    }

    /**
     * Roles the signed in user is allowed to hand out. An admin cannot grant a
     * role that outranks their own.
     */
    public function getRoles(): array
    {
        $roles = Role::latest();

        if (! in_array(auth()->user()->type, $this->access_all_type)) {
            $roles->whereNotIn('name', $this->access_all_type);
        }

        if (! in_array(auth()->user()->type, $this->user_admins)) {
            $roles->whereNotIn('name', $this->user_admins);
        }

        return $roles->get()->mapWithKeys(fn ($r) => [$r->id => $r->label()])->toArray();
    }

    /**
     * Whether the signed in user holds the permission that covers this route.
     * Permission names may list several routes, comma separated.
     */
    public function checkPerm(string $route_admin): bool
    {
        $permission = Permission::nameForRoute($route_admin);

        if ($permission === null) {
            return false;
        }

        return (bool) auth('web')->user()?->can($permission);
    }

    private function withNoneOption(array $items, bool $all, bool $req): array
    {
        if (! $all) {
            return $items;
        }

        return ($req ? [-1 => __('None')] : ['' => __('None')]) + $items;
    }
}
