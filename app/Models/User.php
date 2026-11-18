<?php

namespace App\Models;

use App\Builders\UserBuilder;
use App\Enums\UserTypeEnum;
use App\Models\UserModel as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements LaratrustUser, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRolesAndPermissions, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'full_name', 'email', 'password',
        'provider', 'provider_id', 'email_verified_at',
        'type', 'image', 'phone', 'active',
        'locale', 'theme', 'is_notify', 'birth_date', 'gender', 'last_active',
    ];

    protected $searchable = [
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
        'active' => 'boolean',
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
            ->ofType($userType)
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

    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    /**
     * Record the outgoing hash on every password change, whichever flow made it
     * (forget password, profile, dashboard).
     */
    protected static function booted(): void
    {
        parent::booted();

        static::updated(function (User $user) {
            if (! $user->wasChanged('password') || ! $user->getOriginal('password')) {
                return;
            }

            $user->passwordHistories()->create([
                'old_password' => $user->getOriginal('password'),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255) ?: null,
            ]);
        });
    }

    // -------------------------------------------------------------------------

    public function typeEnum(): ?UserTypeEnum
    {
        return UserTypeEnum::tryFrom((string) $this->type);
    }

    /** Admin or super admin. */
    public function isAdmin(): bool
    {
        return in_array($this->typeEnum(), [UserTypeEnum::Admin, UserTypeEnum::SuperAdmin], true);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->typeEnum()?->isSuperAdmin();
    }

    /** Donors and volunteers: the people who use the app, not the dashboard. */
    public function isClient(): bool
    {
        return in_array($this->typeEnum(), UserTypeEnum::appTypes(), true);
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
