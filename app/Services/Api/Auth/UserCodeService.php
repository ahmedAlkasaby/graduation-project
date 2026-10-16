<?php

namespace App\Services\Api\Auth;

use App\Enums\UserCodeTypeEnum;
use App\Models\User;
use App\Models\UserCode;
use App\Services\AppSettingsService;
use Carbon\CarbonInterface;

/**
 * The one-time codes sent by email, see UserCodeTypeEnum for what each is for.
 *
 * Registration codes are keyed by contact detail rather than by user: the
 * account is only created once the code comes back, and attachToUser() then
 * ties the code to the account it produced and expires it so it cannot be
 * replayed.
 *
 * Codes for an account that already exists (forget password) are keyed by
 * user_id instead.
 */
class UserCodeService
{
    public function __construct(
        private AppSettingsService $settings,
    ) {}

    /**
     * Issue a registration code, or hand back the one already outstanding.
     *
     * Re-sending must not mint a second code: the first one is still valid and
     * may already be in the inbox, so both would work and the older would look
     * broken to whoever typed it.
     */
    public function generate(UserCodeTypeEnum $type, string $email, ?string $phone = null): string
    {
        $existing = $this->pending(type: $type, email: $email);

        if ($existing) {
            return $existing->code;
        }

        return UserCode::create([
            'user_id' => 0,
            'type' => $type,
            'email' => $email,
            'phone' => $phone,
            'code' => $this->randomCode(),
            'code_expire' => $this->expiresAt(),
        ])->code;
    }

    /**
     * The unexpired code matching what the caller typed, or null.
     */
    public function verify(string $code, UserCodeTypeEnum $type, ?string $phone = null, ?string $email = null): ?UserCode
    {
        return $this->pending(type: $type, code: $code, phone: $phone, email: $email);
    }

    
    public function attachToUser(UserCode $userCode, int $userId): void
    {
        $userCode->update([
            'user_id' => $userId,
            'code_expire' => now(),
        ]);
    }

    
    public function expire(UserCode $userCode): void
    {
        $userCode->update(['code_expire' => now()]);
    }

   
    public function issueForUser(User $user, UserCodeTypeEnum $type): string
    {
        UserCode::query()->pendingForUser($user->id, $type)->update(['code_expire' => now()]);

        return UserCode::create([
            'user_id' => $user->id,
            'type' => $type,
            'email' => $user->email,
            'phone' => $user->phone,
            'code' => $this->randomCode(),
            'code_expire' => $this->expiresAt(),
        ])->code;
    }

   
    public function pendingForUser(User $user, UserCodeTypeEnum $type, ?string $code = null): ?UserCode
    {
        return UserCode::query()
            ->pendingForUser($user->id, $type)
            ->when(! is_null($code), fn ($query) => $query->where('code', $code))
            ->latest()
            ->first();
    }

    protected function pending(UserCodeTypeEnum $type, ?string $code = null, ?string $phone = null, ?string $email = null): ?UserCode
    {
        return UserCode::query()
            ->latestNotExpired(type: $type, code: $code, phone: $phone, email: $email)
            ->latest()
            ->first();
    }

    protected function randomCode(): string
    {
        $length = $this->settings->getUserCodeLength();

        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    protected function expiresAt(): CarbonInterface
    {
        return now()->addMinutes($this->settings->getUserCodeLifetimeMinutes());
    }
}
