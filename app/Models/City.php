<?php

namespace App\Models;

use App\Builders\CityBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends SiteModel
{
    use HasFactory;

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

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function newEloquentBuilder($query): CityBuilder
    {
        return new CityBuilder($query);
    }
}
