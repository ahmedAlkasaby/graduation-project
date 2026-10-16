<?php

/**
 * Which SMS provider sends verification codes.
 *
 * The `sms_provider` setting overrides this at runtime; this is the fallback.
 * It defaults to "log" so a fresh install works without an SMS account — every
 * message is written to the log instead of sent.
 */
return [

    'default_provider' => env('SMS_PROVIDER', 'log'),

    'log_channel' => env('SMS_LOG_CHANNEL', 'stack'),

];
