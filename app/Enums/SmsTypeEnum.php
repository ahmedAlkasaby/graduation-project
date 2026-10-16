<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * How the phone number on a registration was proved.
 *
 * `Sms` means this application issued the code and must check it. `Firebase`
 * means the client verified the number with Firebase before calling us, so
 * there is no code of ours to check — which is why registration takes two
 * different paths.
 */
enum SmsTypeEnum: string
{
    use HasOptions;

    case Sms = 'sms';
    case Firebase = 'firebase';

    public function label(): string
    {
        return match ($this) {
            self::Sms => __('SMS'),
            self::Firebase => __('Firebase'),
        };
    }

    /**
     * Whether the caller proved the number elsewhere and we should not look for
     * a code of our own.
     */
    public function verifiedExternally(): bool
    {
        return $this === self::Firebase;
    }

    /**
     * The value a request carried, defaulting to our own SMS flow.
     */
    public static function fromRequest(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::Sms;
    }
}
