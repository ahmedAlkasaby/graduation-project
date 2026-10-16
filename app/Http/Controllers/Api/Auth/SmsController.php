<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiHomeController;
use App\Models\Country;
use App\DTOs\SmsResponseDTO;
use App\Exceptions\SmsException;
use Illuminate\Http\JsonResponse;
use App\Contracts\SmsServiceInterface;
use App\Notifications\EmailVerification;
use App\Services\Api\Auth\UserCodeService;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Api\Auth\Sms\SmsCodeRequest;
use App\Http\Requests\Api\Auth\Sms\SmsSendRequest;


class SmsController extends ApiHomeController
{
    protected UserCodeService $userCodeService;
    protected SmsServiceInterface $smsService;

    public function __construct(UserCodeService $userCodeService, SmsServiceInterface $smsService)
    {
        parent::__construct();
        $this->userCodeService = $userCodeService;
        $this->smsService = $smsService;
    }

    public function smsSend(SmsSendRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->userCodeService->generate($data, $this->sms_minutes);

        if (! $response->status) {
            return $this->errorResponse($this->getMessageError('code_send'));
        }

        if ($data['code_type'] === 'phone') {
            $message = $response->code . ' your verification code to ' . config('app.name');
            $country = Country::where('id', $data['country_id'])->first();
            $mobile  = prefixPhoneWithCountryCode($data['phone'], $country->phone_code);

            try {
                $this->smsService->send($mobile, $message);
            } catch (SmsException $e) {
                return $this->failedResponse($e, $this->getMessageError('code_send'));
            }
        } else {
            Notification::route('mail', $data['email'])->notify(new EmailVerification($response->code));
        }

        return $this->successResponse();
    }

    public function smsCode(SmsCodeRequest $request): JsonResponse
    {
        $userCode = $this->userCodeService->verify(
            $request->code,
            $request->input('type', 'client'),
            $request->phone,
            $request->email
        );

        if ($userCode) {
            $this->userCodeService->createCodeExpire($userCode);
            return $this->successResponse(['code' => $request->code, 'message' => '']);
        }

        $message = $this->getMessageError('code_not_valid');
        return $this->errorResponse($message);
    }

    /**
     * Test endpoint – demonstrates injection usage.
     */
    public function test(SmsServiceInterface $sms): JsonResponse
    {
        try {
            $result = $sms->send('965XXXXXXXX', 'Hello, test message from Laravel.');

            return response()->json([
                'success'    => $result->success,
                'message'    => $result->message,
                'message_id' => $result->messageId,
            ]);
        } catch (SmsException $e) {
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage(),
                'provider' => $e->getProvider(),
            ], 500);
        }
    }
}
