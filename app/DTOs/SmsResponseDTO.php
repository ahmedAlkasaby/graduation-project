<?php

namespace App\DTOs;

/**
 * Standardized response object for all SMS operations.
 *
 * WHY: Controllers and services receive a uniform response regardless of which
 * provider was used. No raw HTTP responses leak into business logic.
 */
class SmsResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?string $messageId = null,
        public readonly ?int $statusCode = null,
        public readonly array $meta = [],
    ) {}

    public static function success(string $message = 'SMS sent successfully', ?string $messageId = null, array $meta = []): static
    {
        return new static(
            success: true,
            message: $message,
            messageId: $messageId,
            statusCode: 200,
            meta: $meta,
        );
    }

    public static function failure(string $message, ?int $statusCode = null, array $meta = []): static
    {
        return new static(
            success: false,
            message: $message,
            messageId: null,
            statusCode: $statusCode,
            meta: $meta,
        );
    }
}
