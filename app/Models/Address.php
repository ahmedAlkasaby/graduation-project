<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Where a model is: the city is required, the region, the free text address
 * and the map point are optional. Any model can own one through AddressTrait.
 */
class Address extends SiteModel
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = ['city_id', 'region_id', 'address', 'latitude', 'longitude'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /** The address on one line: city, region, then the free text (load `city` and `region` first). */
    public function label(): string
    {
        return collect([$this->city?->nameLang(), $this->region?->nameLang(), $this->address])->filter()->implode(' - ');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
