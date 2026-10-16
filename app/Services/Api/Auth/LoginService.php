<?php

namespace App\Services\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Enums\LoginMethodEnum;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Signing in from the app. One field carries either an email address or a phone
 * number, and which one it holds decides the column matched.
 */
class LoginService
{
    
    public function methodFor(?string $identifier): LoginMethodEnum
    {
        return filter_var((string) $identifier, FILTER_VALIDATE_EMAIL)
            ? LoginMethodEnum::Email
            : LoginMethodEnum::Phone;
    }

   
    public function getCredentials(?string $identifier, string $password): array
    {
        $credentials = ['password' => $password];

        if ($this->methodFor($identifier) === LoginMethodEnum::Email) {
            $credentials['email'] = Str::lower((string) $identifier);
        } else {
            $credentials['phone'] = (string) $identifier;
        }

        return $credentials;
    }

   
    public function attemptLogin(array $credentials, string $imei, string $token, string $deviceType): ?AuthenticatedUserDTO
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        Device::updateDevice($user->id, $imei, $token, $deviceType);

        return new AuthenticatedUserDTO(
            user: $user,
            accessToken: $user->createToken('auth_token')->plainTextToken,
        );
    }
}
