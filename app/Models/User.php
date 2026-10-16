<?php

namespace App\Models;

use App\Models\UserModel as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Str;

class User extends Authenticatable implements JWTSubject, LaratrustUser, MustVerifyEmail
{
    use HasApiTokens, HasRolesAndPermissions, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'username', 'name_first', 'name_last', 'email', 'password',
        'provider', 'provider_id', 'provider_token', 'email_verified_at',
        'sms_code', 'sms_code_expire', 'code', 'code_expire',
        'type', 'image', 'country_id', 'city_id', 'phone', 'phone_code',
        'locale', 'theme', 'is_notify', 'birth_date', 'gender', 'last_active',
        'latitude', 'longitude', 'active', 'is_admin', 'is_client',
    ];

    protected $searchable = [
        'name', 'name_first', 'name_last', 'email', 'phone', 'username',
    ];

    /** Matches the users_search_fulltext index. Keep the two in step. */
    protected $fullTextSearchable = [
        'name', 'name_first', 'name_last', 'email', 'phone', 'username',
    ];

    protected $orderByAllowed = [
        'id', 'name', 'email', 'phone', 'created_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['full_name'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'code_expire' => 'datetime',
        'sms_code_expire' => 'datetime',
        'last_active' => 'datetime',
        'birth_date' => 'date',
        'password' => 'hashed',
    ];


    public function getFullNameAttribute(): string
    {
        $parts = trim("{$this->name_first} {$this->name_last}");

        return $parts !== '' ? $parts : (string) ($this->attributes['name'] ?? '');
    }

    public static function generateUserName($username)
    {
        if ($username === null) {
            $username = Str::lower(Str::random(8));
        }

        if (static::where('username', $username)->exists()) {
            return self::generateUserName($username.Str::random(3));
        }

        return $username;
    }

    public static function listUserForSelect(
        $type = null,
        $key = 'id',
        $userType = 'all',
        $columns = ['phone', 'name', 'name_first', 'name_last'],
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

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

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

    public function isActive(): bool
    {
        return (int) $this->active === 1;
    }

    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->full_name,
        ];
    }

    public function newEloquentBuilder($query): \App\Builders\UserBuilder
    {
        return new \App\Builders\UserBuilder($query);
    }
}
