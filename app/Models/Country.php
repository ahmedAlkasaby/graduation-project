<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends SiteModel
{
    use HasFactory;

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
