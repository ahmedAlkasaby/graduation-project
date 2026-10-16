<?php

namespace App\Models;

class Country extends SiteModel
{
    protected $table = 'countries';

    protected $fillable = [
        'name', 'code', 'image', 'phone_code', 'order_id', 'active',
    ];

    protected $searchable = ['code'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
