<?php

namespace App\Services\Api\Auth\ResetPassword;

use App\Models\PasswordReset;
use App\Notifications\PasswordResetSuccessNotification;
use App\Services\Api\Auth\UserCodeService;
use Illuminate\Support\Facades\Notification;

class OtpPasswordResetService extends ResetPasswordMainService
{

    public function handle($request)
    {
        $passwordReset=PasswordReset::findByTokenAndEmail($request->token, $request->email);
        if (!$passwordReset) {
            return [
                'error' => true,
                'field' => 'token'
            ];
        }
        $type = $request->type ?? "client";
        $sms_type = $request->sms_type ?? 'email';
        $field = $this->checkValidateEmailOrPhoneAndGetField($request->email);
        $user = $this->checkEmailInUsersAndGetUser($field, $request->email, $type);
        if (!$field || !$user) {
            return [
                'error' => true,
                'field' => $field
            ];
        }
        if($sms_type != "firebase"){
            if (! $this->validateCode($user, $request->code)) {
                return [
                    'error' => true,
                    'field' => 'code'
                ];
            }
        }
        $this->sendNotificationOfResetPasswordSuccess($request->email, $sms_type);
        return[
            'error'=>false,
            'user'=>$user
        ];
    }

    public function validateCode($user, $code): bool
    {
        return (bool) app(UserCodeService::class)->pendingForUser(
            $user,
            UserCodeService::TYPE_PASSWORD_RESET,
            (string) $code,
        );
    }

    public function sendNotificationOfResetPasswordSuccess($emailOrPhone, $sms_type)
    {
         if ($sms_type == "email") {
                Notification::route('mail', $emailOrPhone)
                    ->notify(new PasswordResetSuccessNotification());
        }
    }
}
