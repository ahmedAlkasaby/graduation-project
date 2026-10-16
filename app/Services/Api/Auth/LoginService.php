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
    /**
     * Which column an identifier should be matched against.
     *
     * Read from the identifier itself, not from the request: registration by
     * phone has no email in the payload, and deciding from `request('email')`
     * there produced credentials with a null phone that could never match.
     */
    public function methodFor(?string $identifier): LoginMethodEnum
    {
        return filter_var((string) $identifier, FILTER_VALIDATE_EMAIL)
            ? LoginMethodEnum::Email
            : LoginMethodEnum::Phone;
    }

    /**
     * Credentials include the audience flag, so an admin signing in on the
     * client app fails at the same point a wrong password does — the response
     * never says which.
     *
     * @return array<string, mixed>
     */
    public function getCredentials(?string $identifier, string $password, string $type = 'client'): array
    {
        $credentials = ['password' => $password];

        if ($this->methodFor($identifier) === LoginMethodEnum::Email) {
            $credentials['email'] = Str::lower((string) $identifier);
        } else {
            $credentials['phone'] = (string) $identifier;
        }

        $credentials['is_admin'] = $type === 'admin' ? 1 : 0;

        return $credentials;
    }

    /**
     * Attempt a sign in and issue a token, or return null.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function attemptLogin(array $credentials, string $imei, string $token, string $deviceType): ?AuthenticatedUserDTO
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();

        // Remember the device so notifications can reach it.
        Device::updateDevice($user->id, $imei, $token, $deviceType);

        return new AuthenticatedUserDTO(
            user: $user,
            accessToken: $user->createToken('auth_token')->plainTextToken,
        );
    }
}
