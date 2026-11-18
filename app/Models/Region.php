<?php

namespace App\Models;

use App\Builders\RegionBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A well-known area (district / markaz) inside a city (governorate). */
class Region extends SiteModel
{
    use HasFactory;

    protected $table = 'regions';

    protected $fillable = [
        'name', 'city_id', 'order_id', 'active', 'latitude', 'longitude',
    ];

    protected $searchable = ['name'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function newEloquentBuilder($query): RegionBuilder
    {
        return new RegionBuilder($query);
    }
}
