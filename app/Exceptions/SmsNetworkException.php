<?php

namespace App\Exceptions;

/**
 * Thrown when the SMS provider cannot be reached (timeout, DNS failure, etc.).
 */
class SmsNetworkException extends SmsException
{
}
