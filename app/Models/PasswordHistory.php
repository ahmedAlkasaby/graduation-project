<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PasswordHistory extends Model
{
    protected $fillable = [
        'user_id',
        'old_password',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'old_password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
