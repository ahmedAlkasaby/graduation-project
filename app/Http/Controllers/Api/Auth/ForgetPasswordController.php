<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\UserFacingException;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Services\Api\Auth\ForgetPasswordService;
use Illuminate\Http\JsonResponse;

class ForgetPasswordController extends ApiHomeController
{
    public function __construct(
        private readonly ForgetPasswordService $forgetPasswordService,
    ) {
        parent::__construct();
    }

    public function forget(ForgetPasswordRequest $request): JsonResponse
    {
        $this->forgetPasswordService->sendCode($request->validated('email'));

        return $this->successResponse();
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->forgetPasswordService->reset(
                $request->validated('email'),
                $request->validated('code'),
                $request->validated('password'),
            );
        } catch (UserFacingException $e) {
            return $this->failedResponse($e);
        }

        return $this->successResponse(null, $this->getMessageSuccess('password'));
    }
}
