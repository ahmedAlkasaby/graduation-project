<?php

namespace App\Models;

use App\Builders\UserCodeBuilder;
use App\Enums\UserCodeTypeEnum;

class UserCode extends SiteModel
{
    protected $table = 'user_codes';

    protected $fillable = [
        'phone',
        'email',
        'code',
        'type',
        'code_expire',
        'user_id',
    ];

    protected $casts = [
        'type' => UserCodeTypeEnum::class,
        'code_expire' => 'datetime',
    ];

    public function newEloquentBuilder($query): UserCodeBuilder
    {
        return new UserCodeBuilder($query);
    }
}
