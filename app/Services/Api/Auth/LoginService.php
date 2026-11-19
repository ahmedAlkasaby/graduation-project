<?php

namespace App\Services\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Enums\LoginMethodEnum;
use App\Enums\UserTypeEnum;
use App\Models\Device;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Signing in from the app. One field carries either an email address or a phone
 * number, and which one it holds decides the column matched.
 */
class LoginService
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

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

    /**
     * The same credentials, narrowed to admin and super admin accounts, so a
     * donor or volunteer signing in to the dashboard fails like a wrong password.
     *
     * @param  array<string, mixed>  $credentials
     * @return array<string, mixed>
     */
    public function restrictToAdmins(array $credentials): array
    {
        $credentials['type'] = fn (Builder $query) => $query->whereIn('type', UserTypeEnum::adminValues());

        return $credentials;
    }

    /**
     * $toDashboard: signing in to the dashboard rather than the app, which
     * decides the role flag checked (can_login_web or can_login_mobile).
     */
    public function attemptLogin(array $credentials, string $imei, string $token, string $deviceType, bool $toDashboard = false): ?AuthenticatedUserDTO
    {
        if (! Auth::attempt([...$credentials, 'active' => true])) {
            return null;
        }

        $user = Auth::user();

        // An admin whose roles all keep them off this platform gets no token.
        if (! ($toDashboard ? $user->canLoginWeb() : $user->canLoginMobile())) {
            return null;
        }

        Device::updateDevice($user->id, $imei, $token, $deviceType);

        return $this->tokenService->issue($user);
    }
}
