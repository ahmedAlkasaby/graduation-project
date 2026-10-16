<?php

namespace App\Services\Api\Auth;

use App\Enums\UserCodeTypeEnum;
use App\Exceptions\UserFacingException;
use App\Models\User;
use App\Notifications\PasswordResetRequestNotification;
use App\Notifications\PasswordResetSuccessNotification;
use App\Traits\ResponseMessageTrait;
use Illuminate\Support\Facades\DB;

/**
 * Resetting a forgotten password with a code sent to the account's email.
 */
class ForgetPasswordService
{
    use ResponseMessageTrait;

    public function __construct(
        private UserCodeService $userCodes,
    ) {}

    /**
     * Email a fresh reset code; any earlier code stops working.
     */
    public function sendCode(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        $code = $this->userCodes->issueForUser($user, UserCodeTypeEnum::ForgetPassword);

        $user->notify(new PasswordResetRequestNotification($code));
    }

    /**
     * Swap the password if the code checks out.
     *
     * The password change, burning the code and signing out every device are
     * one transaction, so the code can never outlive the reset it allowed.
     * The old hash is recorded by User::booted().
     *
     * @throws UserFacingException when the code is wrong or expired
     */
    public function reset(string $email, string $code, string $password): User
    {
        $user = User::where('email', $email)->firstOrFail();

        $userCode = $this->userCodes->pendingForUser($user, UserCodeTypeEnum::ForgetPassword, $code);

        if (! $userCode) {
            throw new UserFacingException($this->getMessageError('code_not_valid'));
        }

        DB::transaction(function () use ($user, $userCode, $password) {
            $user->update(['password' => $password]);
            $this->userCodes->expire($userCode);
            $user->tokens()->delete();
        });

        $user->notify(new PasswordResetSuccessNotification);

        return $user;
    }
}
