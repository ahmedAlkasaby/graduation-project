<?php

namespace App\Models;

class City extends SiteModel
{
    protected $table = 'cities';

    protected $fillable = [
        'name', 'country_id', 'order_id', 'active', 'latitude', 'longitude',
    ];

    protected $searchable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function newEloquentBuilder($query): \App\Builders\CityBuilder
    {
        return new \App\Builders\CityBuilder($query);
    }
}
