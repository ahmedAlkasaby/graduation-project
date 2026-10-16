<?php

namespace App\Models;

use App\Models\UserModel as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements LaratrustUser, MustVerifyEmail
{
    use HasApiTokens, HasRolesAndPermissions, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'full_name', 'email', 'password',
        'provider', 'provider_id', 'provider_token', 'email_verified_at',
        'type', 'image', 'phone',
        'locale', 'theme', 'is_notify', 'birth_date', 'gender', 'last_active',
        'is_admin',
    ];

    protected $searchable = [
        'full_name', 'email', 'phone',
    ];

    /** Matches the users_search_fulltext index. Keep the two in step. */
    protected $fullTextSearchable = [
        'full_name', 'email', 'phone',
    ];

    protected $orderByAllowed = [
        'id', 'full_name', 'email', 'phone', 'created_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active' => 'datetime',
        'birth_date' => 'date',
        'password' => 'hashed',
    ];


    public static function listUserForSelect(
        $type = null,
        $key = 'id',
        $userType = 'all',
        $columns = ['phone', 'full_name'],
        $queryBuilder = null,
    ) {
        $query = $queryBuilder ?? static::query();

        $columns = array_unique(array_merge([$key], $columns));

        $items = $query
            ->select($columns)
            ->userType(null, $userType)
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->$key => trim($item->full_name.' '.$item->phone),
            ])
            ->toArray();

        return match ($type) {
            'default' => defaultOption() + $items,
            'filter' => filterOption() + $items,
            default => $items,
        };
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function userMetas()
    {
        return $this->hasMany(UserMeta::class);
    }

    public function metas()
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    // -------------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    /** Anyone who is not an admin is a client of the app. */
    public function isClient(): bool
    {
        return ! $this->isAdmin();
    }

    public function newEloquentBuilder($query): \App\Builders\UserBuilder
    {
        return new \App\Builders\UserBuilder($query);
    }
}
