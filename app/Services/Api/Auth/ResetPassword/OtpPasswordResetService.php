<?php

namespace App\Services\Api\Auth\ResetPassword;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetSuccessNotification;
use Carbon\Carbon;
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
            
            $user = $this->validateCodeInUsers($field, $request->code, $passwordReset->email, $type);
            if (!$user) {
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
    public function validateCodeInUsers($field,$code,$passwordResetEmail,$type="client")
    {
            $is_field = "is_" . $type;
            $user_code = User::where($is_field, 1)->where('sms_code', $code)->where('sms_code_expire', '>=', Carbon::now())->where($field, $passwordResetEmail)->first();
            if (!$user_code) {
                return false;
            }
            return $user_code;
    }
    public function sendNotificationOfResetPasswordSuccess($emailOrPhone, $sms_type)
    {
         if ($sms_type == "email") {
                Notification::route('mail', $emailOrPhone)
                    ->notify(new PasswordResetSuccessNotification());
        }
    }
}
