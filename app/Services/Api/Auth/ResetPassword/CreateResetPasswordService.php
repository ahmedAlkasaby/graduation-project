<?php

namespace App\Services\Api\Auth\ResetPassword;

use App\Models\PasswordReset;
use App\Notifications\PasswordResetRequestNotification;
use App\Services\Api\Auth\UserCodeService;
use Illuminate\Support\Facades\Notification;

class CreateResetPasswordService extends ResetPasswordMainService
{
    public function handle($request)
    {
        $type = $request->type ?? "client";
        $sms_type = $request->sms_type ?? 'email';
        $field = $this->checkValidateEmailOrPhoneAndGetField($request->email);
        $user = $this->checkEmailInUsersAndGetUser($field, $request->email, $type);
        if (!$user || ! $field) {
            return [
                'error' => true,
                'field' => $field
            ];
        }
        $token = PasswordReset::createToken($request->email);

        if ($sms_type != "firebase") {
            $otp = $this->createOtp($user);
            $this->sendOtp($request->email, $otp, $sms_type);
        }
        return [
            'error' => false,
            'token' => $token,
        ];
    }


    public function sendOtp($emailOrPhone, $otp, $sms_type)
    {
         if ($sms_type == "email") {
                Notification::route('mail', $emailOrPhone)
                    ->notify(new PasswordResetRequestNotification($otp));
            }elseif ($sms_type == "sms") {
                $message = $otp . ' ' . ('your verification code to') . ' ' . config('app.name');
                $this->sendSms($emailOrPhone, $message, $this->phone_code_default);
            }
    }

    /**
     * The reset code lives in user_codes, tied to the account by user_id.
     */
    public function createOtp($user): string
    {
        return app(UserCodeService::class)->issueForUser(
            $user,
            UserCodeService::TYPE_PASSWORD_RESET,
            (int) $this->sms_minutes,
        );
    }
}
