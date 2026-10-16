<?php

namespace App\Channels;

use App\DTOs\DataBaseNotificationDTO;
use App\DTOs\NotificationMobileDTO;
use App\Models\Device;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;


class FirebaseChannel
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

    /**
     * @param  array<int, string>  $tokens
     * @return int  tokens the message was accepted for
     */
    public function send(array $tokens, NotificationMobileDTO $dto, DataBaseNotificationDTO $content): int
    {
        if ($tokens === []) {
            return 0;
        }

        $title = $this->line($content->title);
        $body = $this->line($content->message);

        $notification = FirebaseNotification::create($title, $body, $content->image ?: null);

        $data = array_map(
            fn ($value) => is_scalar($value) || $value === null ? (string) $value : json_encode($value),
            $dto->toArray() + ['title' => $title, 'body' => $body],
        );

        $messages = [];

        foreach ($tokens as $token) {
            $messages[] = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);
        }

        $report = $this->messaging->sendAll($messages);

        foreach ($report->failures()->getItems() as $failure) {
            if (str_contains($failure->error()->getMessage(), 'not a valid FCM registration token')) {
                Device::where('token', $failure->target()->value())->delete();
            }
        }

        return $report->successes()->count();
    }


    protected function line(array|string|null $value): string
    {
        if (! is_array($value)) {
            return (string) $value;
        }

        return (string) ($value[app()->getLocale()] ?? $value['en'] ?? reset($value) ?: '');
    }
}
