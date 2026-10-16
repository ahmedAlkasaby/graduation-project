<?php

namespace App\Services\Api\Auth;

use App\Models\UserCode;

/**
 * The one-time codes that prove someone owns a phone number or an email before
 * an account exists for them.
 *
 * Rows are keyed by contact detail rather than by user: the account is only
 * created once the code comes back, and attachToUser() then ties the code to
 * the account it produced and expires it so it cannot be replayed.
 */
class UserCodeService
{
    public const DEFAULT_LIFETIME_MINUTES = 10;

    public const DEFAULT_LENGTH = 4;

    /**
     * Issue a code, or hand back the one already outstanding.
     *
     * Re-sending must not mint a second code: the first one is still valid and
     * may already be on its way to the handset, so both would work and the
     * older would look broken to whoever typed it.
     *
     * @return object{status: bool, code: string}  status false = an existing code was reused
     */
    public function generate(array $data, int $minutes = self::DEFAULT_LIFETIME_MINUTES, int $length = self::DEFAULT_LENGTH): object
    {
        $existing = $this->pending(
            type: $data['type'],
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
        );

        if ($existing) {
            return (object) ['status' => false, 'code' => $existing->code];
        }

        $userCode = UserCode::create($data + [
            'code' => $this->randomCode($length),
            'code_expire' => now()->addMinutes($minutes),
        ]);

        return (object) ['status' => true, 'code' => $userCode->code];
    }

    /**
     * The unexpired code matching what the caller typed, or null.
     */
    public function verify(string $code, string $type, ?string $phone = null, ?string $email = null): ?UserCode
    {
        return $this->pending(type: $type, code: $code, phone: $phone, email: $email);
    }

    /**
     * Tie a code to the account it created and burn it in the same write.
     */
    public function attachToUser(UserCode $userCode, int $userId): void
    {
        $userCode->update([
            'user_id' => $userId,
            'code_expire' => now(),
        ]);
    }

    public function createCodeExpire(UserCode $userCode): void
    {
        $userCode->update(['code_expire' => now()]);
    }

    /**
     * Latest unexpired, unclaimed code for a contact detail.
     *
     * Named arguments throughout: the builder takes type first and the four
     * parameters are all nullable strings, so a positional call that drifts out
     * of order fails silently rather than loudly.
     */
    protected function pending(string $type, ?string $code = null, ?string $phone = null, ?string $email = null): ?UserCode
    {
        return UserCode::query()
            ->latestNotExpired(type: $type, code: $code, phone: $phone, email: $email)
            ->latest()
            ->first();
    }

    protected function randomCode(int $length): string
    {
        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
