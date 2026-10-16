<?php

namespace App\Models;


class UserCode extends SiteModel
{

    protected $table = 'user_codes';
    protected $fillable = [
        'country_code',
        'full_name',
        'phone',
        'email',
        'code',
        'type',
        'code_expire',
        'user_id',
    ];

    protected $casts = [
        'code_expire' => 'datetime',
    ];

    public function newEloquentBuilder($query): \App\Builders\UserCodeBuilder
    {
        return new \App\Builders\UserCodeBuilder($query);
    }
}
