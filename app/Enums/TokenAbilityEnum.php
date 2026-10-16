<?php

namespace App\Enums;

/**
 * The access token calls the API; the refresh token can only be traded for a
 * new pair at POST /refresh.
 */
enum TokenAbilityEnum: string
{
    case AccessToken = 'access_token';
    case RefreshToken = 'refresh_token';
}
