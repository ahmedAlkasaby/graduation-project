<?php

namespace App\Services\Api\Auth;

use App\Contracts\SocialTokenVerifierInterface;
use App\DTOs\AuthenticatedUserDTO;
use App\DTOs\SocialUserDTO;
use App\Enums\SocialProviderEnum;
use App\Models\Device;
use App\Models\User;
use App\Services\Api\Auth\Social\AppleTokenVerifier;
use App\Services\Api\Auth\Social\FacebookTokenVerifier;
use App\Services\Api\Auth\Social\GoogleTokenVerifier;


class SocialLoginService
{
   
    public function login(
        SocialProviderEnum $provider,
        string $socialToken,
        string $imei,
        string $deviceToken,
        string $deviceType,
    ): ?AuthenticatedUserDTO {
        $socialUser = $this->verifierFor($provider)->verify($socialToken);

        if (! $socialUser instanceof SocialUserDTO) {
            return null;
        }

        $user = $this->findOrCreateUser($provider, $socialUser);

        Device::updateDevice($user->id, $imei, $deviceToken, $deviceType);

        return new AuthenticatedUserDTO(
            user: $user,
            accessToken: $user->createToken('api-token')->plainTextToken,
        );
    }

    private function verifierFor(SocialProviderEnum $provider): SocialTokenVerifierInterface
    {
        return app(match ($provider) {
            SocialProviderEnum::Google => GoogleTokenVerifier::class,
            SocialProviderEnum::Facebook => FacebookTokenVerifier::class,
            SocialProviderEnum::Apple => AppleTokenVerifier::class,
        });
    }

    private function findOrCreateUser(SocialProviderEnum $provider, SocialUserDTO $socialUser): User
    {
        $profile = [
            'email' => $socialUser->email,
            'full_name' => $socialUser->displayName(),
        ];

        $user = User::where('provider', $provider->value)
            ->where('provider_id', $socialUser->id)
            ->first();

        if ($user) {
            $user->update($profile);

            return $user;
        }

        return User::create($profile + [
            'provider' => $provider->value,
            'provider_id' => $socialUser->id,
            'type' => 'client',
            'is_admin' => 0,
        ]);
    }
}
