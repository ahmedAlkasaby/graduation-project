<?php

namespace App\Exceptions;

/**
 * Thrown when the provider returns a server-side error (5xx, unexpected response, etc.).
 */
class SmsProviderException extends SmsException
{
}
