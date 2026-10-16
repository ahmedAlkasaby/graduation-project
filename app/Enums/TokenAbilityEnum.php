<?php

namespace App\Enums;

/**
 * The Sanctum abilities a token can carry.
 *
 * An access token calls the API and a refresh token only trades itself for a
 * new pair, so a leaked refresh token can never call the API and a leaked
 * access token can never extend its own life.
 */
enum TokenAbilityEnum: string
{
    case AccessApi = 'access-api';
    case IssueAccessToken = 'issue-access-token';
}
