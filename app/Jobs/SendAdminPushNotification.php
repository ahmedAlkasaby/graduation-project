<?php

namespace App\Jobs;

use App\DTOs\DataBaseNotificationDTO;
use App\DTOs\NotificationMobileDTO;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Pushes one admin notification to one admin's devices.
 *
 * One job per admin rather than one for the whole list: a device token that
 * has gone stale should not stop the other admins being notified, and a retry
 * should not re-push to everyone who already received it.
 */
class SendAdminPushNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public int $adminId,
        public DataBaseNotificationDTO $notification,
    ) {
        //
    }

    public function handle(FirebaseService $firebase): void
    {
        $firebase->sendNotificationToDevices(
            $this->adminId,
            new NotificationMobileDTO,
            $this->notification,
        );
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('Admin push notification failed', [
            'admin_id' => $this->adminId,
            'message' => $e->getMessage(),
        ]);
    }
}
