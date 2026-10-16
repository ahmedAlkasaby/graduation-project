<?php

namespace App\Models;

use App\Builders\LogBuilder;

/**
 * One column a user changed, with the value before and after. Written from the
 * model events on SiteModelDefault — see App\Services\ModelChangeLogger.
 */
class Log extends SiteModelDefault
{
    protected $table = 'logs';

    protected $fillable = [
        'user_id', 'user_device_id', 'loggable_id', 'loggable_type', 'key', 'value_old', 'value',
    ];

    protected $orderByAllowed = ['id', 'created_at'];

    public function newEloquentBuilder($query): LogBuilder
    {
        return new LogBuilder($query);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function userDevice()
    {
        return $this->belongsTo(UserDevice::class);
    }
}
