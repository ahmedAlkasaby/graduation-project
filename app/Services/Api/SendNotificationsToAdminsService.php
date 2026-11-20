<?php

namespace App\Services\Api;

use App\DTOs\DataBaseNotificationDTO;
use App\Jobs\SendAdminPushNotification;
use App\Notifications\AdminNotification;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Notification;


class SendNotificationsToAdminsService
{
    public function __construct(
        protected UserRepository $userRepository,
    ) {}

    public function notifyAdmins(
        DataBaseNotificationDTO $notification,
        bool $sendDatabaseNotification = true,
        bool $sendPushNotification = true,
    ): void {
        if ($sendDatabaseNotification) {
            Notification::send(
                $this->userRepository->getAdminNotifiable(),
                new AdminNotification($notification->toArray())
            );
        }


        if (! $sendPushNotification || settings()->get('ssl_certificate') !== 'yes') {
            return;
        }


        foreach ($this->userRepository->getAdminNotifiableIds() as $adminId) {
            SendAdminPushNotification::dispatch((int) $adminId, $notification);
        }
    }
}
