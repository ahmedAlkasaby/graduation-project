<?php

namespace App\Models;

use App\Builders\ActivityLogBuilder;

/**
 * One request a user made. Written by the CheckLastActive middlewares; nothing
 * writes to it by hand, and nothing updates a row once it exists.
 */
class ActivityLog extends SiteModelDefault
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'user_device_id', 'activity_loggable_id', 'activity_loggable_type',
        'type', 'route', 'status', 'action',
    ];

    protected $orderByAllowed = ['id', 'created_at'];

    public function newEloquentBuilder($query): ActivityLogBuilder
    {
        return new ActivityLogBuilder($query);
    }

    public function activityLoggable()
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
