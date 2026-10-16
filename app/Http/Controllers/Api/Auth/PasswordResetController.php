<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiHomeController;

use App\Http\Requests\Api\Auth\PasswordReset\CreateRestPasswordRequest;
use App\Http\Requests\Api\Auth\PasswordReset\OtpPasswordResetRequest;
use App\Http\Requests\Api\Auth\PasswordReset\ResetRequest;
use App\Http\Resources\UserResource;
use App\Models\PasswordReset;
use App\Services\Api\Auth\ResetPassword\CreateResetPasswordService;
use App\Services\Api\Auth\ResetPassword\OtpPasswordResetService;
use App\Services\Api\Auth\ResetPassword\ResetPasswordMainService;
use App\Traits\SmsTrait;


class PasswordResetController extends ApiHomeController
{
    use SmsTrait;

    private $createResetPasswordService;
    private $otpPasswordResetService;
    private $resetPasswordMainService;
    public function __construct(CreateResetPasswordService $createResetPasswordService, OtpPasswordResetService $otpPasswordResetService, ResetPasswordMainService $resetPasswordMainService)
    {
        parent::__construct();
        $this->createResetPasswordService = $createResetPasswordService;
        $this->otpPasswordResetService = $otpPasswordResetService;
        $this->resetPasswordMainService = $resetPasswordMainService;
    }

  
    public function create(CreateRestPasswordRequest $request)
    {
        $result = $this->createResetPasswordService->handle($request);
        if ($result['error']) {
            return $this->errorResponse(
                $this->getMessageError($result['field'])
            );
        }
        return $this->successResponse([
            'code' => $result['token'],
            'message' => __('Success'),
        ]);
    }

     public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            $message = $this->getMessageError("token");
            return $this->errorResponse($message);
        }
        if (! $this->createResetPasswordService->checkTimeOfResetPassword($passwordReset)) {
            $message = $this->getMessageError("token");
            return $this->errorResponse($message);
        }
        return $this->successResponse($token);
    }

    public function otp(OtpPasswordResetRequest $request)
    {
        $result=$this->otpPasswordResetService->handle($request);
        if($result['error']){
            return $this->errorResponse(
                $this->getMessageError($result['field'])
            );
        }
        $data = ['user' => new UserResource($result['user'])];
        return $this->successResponse($data);
    }

    public function reset(ResetRequest $request)
    {
        $result=$this->resetPasswordMainService->handle($request);
        if($result['error']){
            return $this->errorResponse(
                $this->getMessageError($result['field'])
            );
        }
        $data = ['user' => new UserResource($result['user'])];
        return $this->successResponse($data);
    }


    
}
