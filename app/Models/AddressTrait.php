<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Gives a model one optional address (the `addresses` table, morphed on
 * `model_type` / `model_id`).
 */
trait AddressTrait
{
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'model');
    }
}
