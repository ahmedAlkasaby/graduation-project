<?php
namespace App\Channels;

use App\DTOs\DataBaseNotificationDTO as DTOsDataBaseNotificationDTO;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class DataBaseNotificationChannel
{   public function send($userIds, DTOsDataBaseNotificationDTO $notifcationData)
    {
        $notificationClass = 'App\Notifications\\' . notificationModelName($notifcationData->type);
        User::active()
            ->whereIn('id', $userIds)
            ->select('id')
            ->chunk(9999, function ($users) use ($notificationClass, $notifcationData) {
                Notification::send($users, new $notificationClass($notifcationData));
            });
    }
 }