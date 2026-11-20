<?php

namespace App\Exceptions;

use Exception;

/**
 * Base exception for all SMS-related errors.
 *
 * WHY: Meaningful, domain-specific exceptions instead of generic ones.
 * Allows catching SMS errors independently from other application exceptions.
 */
class SmsException extends Exception
{
    protected string $provider;
    protected array $context;

    public function __construct(string $message, string $provider = '', int $code = 0, array $context = [], ?Exception $previous = null)
    {
        $this->provider = $provider;
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
