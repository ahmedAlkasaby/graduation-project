<?php

namespace App\Services;

use App\DTOs\NotificationMobileDTO;
use App\DTOs\DataBaseNotificationDTO;
use App\Models\Device;
use Illuminate\Notifications\DatabaseNotification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;




class FirebaseService
{
    protected Messaging $messaging;

    public function __construct()
    {
        $this->messaging = (new Factory)
            ->withServiceAccount(config('services.firebase.credentials'))
            ->createMessaging();
    }

    public function getMessaging(): Messaging
    {
        return $this->messaging;
    }

    public function sendNotificationToDevices($userId, NotificationMobileDTO $notificationMobileDTO, DataBaseNotificationDTO $dataBaseNotificationDTO): int
    {
        $messaging = $this->getMessaging();
        $titleString = is_array($dataBaseNotificationDTO->title['en']) ? ($dataBaseNotificationDTO->title['en'] ?? '') : (string)$dataBaseNotificationDTO->title;
        $messageString = is_array($dataBaseNotificationDTO->message['en']) ? ($dataBaseNotificationDTO->message['en'] ?? '') : (string)$dataBaseNotificationDTO->message;
        $imageString = $dataBaseNotificationDTO->image ? (string)$dataBaseNotificationDTO->image : null;

        $notification = FirebaseNotification::create($titleString, $messageString, $imageString);
        $badge = DatabaseNotification::where('notifiable_id', $userId)
            ->where('notifiable_type', 'users')
            ->whereNull('read_at')
            ->count();
        $badgeCount = (int) ($badge + 1);
        $notificationMobileDTO = $notificationMobileDTO->withBadge($badgeCount);
        $tokens = Device::where('user_id', $userId)->pluck('token')->toArray();
        if (!empty($tokens)) {
            $messages = [];
            foreach ($tokens as $deviceToken) {
                $messages[] = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification($notification)
                    ->withData(array_merge($notificationMobileDTO->toArray(), [
                        'title' => $titleString,
                        'body'  => $messageString,
                    ]));
            }

            $sendReport = $messaging->sendAll($messages);

            foreach ($sendReport->failures()->getItems() as $failure) {
                $errorMessage = $failure->error()->getMessage();
                $failedToken = $failure->target()->value();

                if (strpos($errorMessage, 'not a valid FCM registration token') !== false) {
                    Device::where('token', $failedToken)->delete();
                }
            }

            $notifi_success = $sendReport->successes()->count();
        } else {
            $notifi_success = 0;
        }

        return $notifi_success;
    }
}
