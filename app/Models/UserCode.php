<?php

namespace App\Models;


class UserCode extends SiteModel
{

    protected $table = 'user_codes';
    protected $fillable = [
        'country_code',
        'name',
        'username',
        'name_first',
        'name_last',
        'phone',
        'email',
        'code',
        'type',
        'code_expire',
        'user_id',
    ];

    protected $appends = array('name');
    protected $casts = [
        'code_expire' => 'datetime',
    ];

    public function getNameAttribute()
    {
        return "{$this->name_first} {$this->name_last}";
    }
    public function setNameAttribute($value)
    {
        $first = $this->attributes['name_first'] ?? '';
        $last  = $this->attributes['name_last'] ?? '';

        $this->attributes['name'] = trim($first . ' ' . $last);
    }

    

    public function newEloquentBuilder($query): \App\Builders\UserCodeBuilder
    {
        return new \App\Builders\UserCodeBuilder($query);
    }
}
