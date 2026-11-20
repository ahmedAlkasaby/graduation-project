<?php

namespace App\Services;

use App\Channels\DataBaseNotificationChannel;
use App\Channels\FirebaseChannel;
use App\DTOs\DataBaseNotificationDTO;
use App\DTOs\NotificationMobileDTO;
use App\Models\Device;
use Illuminate\Notifications\DatabaseNotification;


class NotificationService
{
    public function __construct(
        protected FirebaseChannel $firebaseChannel,
        protected DataBaseNotificationChannel $databaseChannel,
    ) {
        //
    }


    public function sendToUsers(
        array $userIds,
        NotificationMobileDTO $dto,
        DataBaseNotificationDTO $content,
        bool $saveInDatabase = true,
        bool $sendFirebase = true,
    ): int {
        $userIds = array_values(array_unique(array_filter($userIds)));

        if ($userIds === []) {
            return 0;
        }

        if ($saveInDatabase) {
            $this->databaseChannel->send($userIds, $content);
        }

        if (! $sendFirebase) {
            return 0;
        }

        $badges = $this->unreadCounts($userIds);

        $tokensByUser = Device::whereIn('user_id', $userIds)
            ->whereNotNull('token')
            ->get(['user_id', 'token'])
            ->groupBy('user_id');

        $sent = 0;

        foreach ($tokensByUser as $userId => $devices) {
            $tokens = $devices->pluck('token')->all();

            if ($tokens === []) {
                continue;
            }

            $sent += $this->firebaseChannel->send(
                $tokens,
                $dto->withBadge(($badges[$userId] ?? 0) + 1),
                $content,
            );
        }

        return $sent;
    }


    protected function unreadCounts(array $userIds): array
    {
        return DatabaseNotification::query()
            ->whereIn('notifiable_id', $userIds)
            ->where('notifiable_type', 'users')
            ->whereNull('read_at')
            ->selectRaw('notifiable_id, count(*) as aggregate')
            ->groupBy('notifiable_id')
            ->pluck('aggregate', 'notifiable_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }
}
