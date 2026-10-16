<?php

namespace App\Services\Api\Auth\ResetPassword;

use App\Http\Controllers\Api\ApiHomeController;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetRequestNotification;
use App\Traits\SmsTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class CreateResetPasswordService extends ResetPasswordMainService
{
    public function handle($request)
    {
        $country_id  =  1;
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
            $otp = $this->createOtpInUsers($user);
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

    public function createOtpInUsers($user)
    {
         $otp = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
         
         $user->update([
             'sms_code' => $otp,
             'sms_code_expire' => Carbon::now()->addMinutes(
                 $this->sms_minutes
             )
         ]);
         return $otp;
    }

   
     
}
