<?php

namespace App\Models;

use App\Builders\RoleBuilder;
use Illuminate\Support\Facades\DB;
use Laratrust\Models\Role as LaratrustRole;

class Role extends LaratrustRole
{
    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    protected $searchable = [
        'name', 'display_name',
    ];

    /** Role does not extend SiteModel, so it opts into the shared builder here. */
    public function newEloquentBuilder($query): RoleBuilder
    {
        return new RoleBuilder($query);
    }

    public function getSearchable(): array
    {
        return $this->searchable;
    }

    public function getOrderByAllowed(): array
    {
        return ['id', 'name', 'display_name', 'created_at'];
    }

    public static function getRoleCount($role_id = 1): int
    {
        return DB::table(config('laratrust.tables.role_user'))
            ->where(config('laratrust.foreign_keys.role'), $role_id)
            ->where('user_type', (new User)->getMorphClass())
            ->count();
    }

    public function label(): string
    {
        return $this->display_name ?: $this->name;
    }
}
