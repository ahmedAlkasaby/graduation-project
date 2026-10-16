<?php

namespace App\DTOs;

readonly class NotificationMobileDTO    
{
     public function __construct(
        public int $badge = 1,
        public string $sound = 'default',
        public string $screen = 'screenA',
        public string $priority = 'high',
        public bool $mutableContent = true,
        public bool $contentAvailable = true
    ) {}

    public function toArray(): array
    {
        return [
            'badge' => $this->badge,
            'sound' => $this->sound,
            'screen' => $this->screen,
            'priority' => $this->priority,
            'mutable-content' => $this->mutableContent,
            'content-available' => $this->contentAvailable,
        ];
    }

    public function withBadge(int $badge): self
    {
        return new self(
            badge: $badge,
            sound: $this->sound,
            screen: $this->screen,
            priority: $this->priority,
            mutableContent: $this->mutableContent,
            contentAvailable: $this->contentAvailable
        );
    }



  
}