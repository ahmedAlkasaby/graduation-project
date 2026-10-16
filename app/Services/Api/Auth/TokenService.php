<?php

namespace App\Services\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Enums\TokenAbilityEnum;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Every login creates an access token and a refresh token with the same name,
 * so the pair can be found and deleted together.
 */
class TokenService
{
    public function issue(User $user): AuthenticatedUserDTO
    {
        $pairName = (string) Str::uuid();
        $accessTokenExpiresAt = now()->addMinutes((int) config('sanctum.access_token_expiration'));
        $refreshTokenExpiresAt = now()->addMinutes((int) config('sanctum.refresh_token_expiration'));

        $accessToken = $user->createToken($pairName, [TokenAbilityEnum::AccessToken->value], $accessTokenExpiresAt);
        $refreshToken = $user->createToken($pairName, [TokenAbilityEnum::RefreshToken->value], $refreshTokenExpiresAt);

        return new AuthenticatedUserDTO(
            user: $user,
            accessToken: $accessToken->plainTextToken,
            refreshToken: $refreshToken->plainTextToken,
            accessTokenExpiresAt: $accessTokenExpiresAt,
            refreshTokenExpiresAt: $refreshTokenExpiresAt,
        );
    }

    public function refresh(User $user, PersonalAccessToken $refreshToken): AuthenticatedUserDTO
    {
        return DB::transaction(function () use ($user, $refreshToken) {
            $this->revoke($refreshToken);

            return $this->issue($user);
        });
    }

    public function revoke(PersonalAccessToken $token): void
    {
        $token->tokenable->tokens()->where('name', $token->name)->delete();
    }
}
