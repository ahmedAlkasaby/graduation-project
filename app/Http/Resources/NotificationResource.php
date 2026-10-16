<?php

namespace App\Http\Resources;

/**
 * Wraps Laravel's own DatabaseNotification. The payload a notification stores
 * is free-form, so everything below reads defensively.
 */
class NotificationResource extends MainResource
{
    public function toArray($request): array
    {
        $data = $this->data ?? [];
        $type = $data['type'] ?? 'admin';

        return [
            'id' => $this->id,
            'title' => $this->translated($data['title'] ?? null),
            'body' => $this->translated($data['body'] ?? null),
            'type' => $type,
            'type_name' => labelForValue($type),
            'model_id' => $data['model_id'] ?? null,
            'model' => $data['model'] ?? null,
            'read_at' => $this->read_at?->toDateTimeString(),
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),
        ];
    }
}
