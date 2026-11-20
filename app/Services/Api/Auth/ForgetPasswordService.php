<?php

namespace App\Services\Api\Auth;

use App\Enums\UserCodeTypeEnum;
use App\Exceptions\UserFacingException;
use App\Models\User;
use App\Notifications\PasswordResetRequestNotification;
use App\Notifications\PasswordResetSuccessNotification;
use App\Traits\ResponseMessageTrait;
use Illuminate\Support\Facades\DB;


class ForgetPasswordService
{
    use ResponseMessageTrait;

    public function __construct(
        private UserCodeService $userCodes,
    ) {}

   
    public function sendCode(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        $code = $this->userCodes->issueForUser($user, UserCodeTypeEnum::ForgetPassword);

        $user->notify(new PasswordResetRequestNotification($code));
    }

    
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
