<?php

namespace App\Services\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Enums\TokenAbilityEnum;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;


class TokenService
{
    private const SESSION_PREFIX = 'session:';

    public function issue(User $user): AuthenticatedUserDTO
    {
        $sessionName = self::SESSION_PREFIX.Str::uuid();
        $accessTokenExpiresAt = now()->addMinutes((int) config('sanctum.access_token_expiration'));
        $refreshTokenExpiresAt = now()->addMinutes((int) config('sanctum.refresh_token_expiration'));

        $accessToken = $user->createToken($sessionName, [TokenAbilityEnum::AccessApi->value], $accessTokenExpiresAt);
        $refreshToken = $user->createToken($sessionName, [TokenAbilityEnum::IssueAccessToken->value], $refreshTokenExpiresAt);

        return new AuthenticatedUserDTO(
            user: $user,
            accessToken: $accessToken->plainTextToken,
            refreshToken: $refreshToken->plainTextToken,
            accessTokenExpiresAt: $accessTokenExpiresAt,
            refreshTokenExpiresAt: $refreshTokenExpiresAt,
        );
    }

    /**
     * Trade a refresh token for a new pair. The old pair is revoked, so each
     * refresh token works exactly once.
     */
    public function refresh(User $user, PersonalAccessToken $refreshToken): AuthenticatedUserDTO
    {
        return DB::transaction(function () use ($user, $refreshToken) {
            $this->revoke($refreshToken);

            return $this->issue($user);
        });
    }

    /**
     * Revoke the given token and its pair. Tokens issued before pairing existed
     * share a generic name across sessions, so only the token itself goes.
     */
    public function revoke(PersonalAccessToken $token): void
    {
        if (! Str::startsWith($token->name, self::SESSION_PREFIX)) {
            $token->delete();

            return;
        }

        PersonalAccessToken::query()
            ->where('tokenable_type', $token->tokenable_type)
            ->where('tokenable_id', $token->tokenable_id)
            ->where('name', $token->name)
            ->delete();
    }
}
