<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Api\Auth\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\Auth\SocialLoginService;
use Illuminate\Http\JsonResponse;

/**
 * Signing in with a Google, Facebook or Apple token. Validation lives in
 * SocialLoginRequest and the sign-in itself in SocialLoginService.
 */
class SocialLoginController extends ApiHomeController
{
    public function __construct(
        private readonly SocialLoginService $socialLoginService,
    ) {
        parent::__construct();
    }

    public function tokenLogin(SocialLoginRequest $request): JsonResponse
    {
        $result = $this->socialLoginService->login(
            $request->provider(),
            $request->validated('token'),
            $request->validated('imei'),
            $request->validated('firebase_token'),
            $request->validated('device_type'),
        );

        if (! $result instanceof AuthenticatedUserDTO) {
            return $this->errorResponse(__('auth.invalid_token'), 401);
        }

        return $this->resourceResponse(new UserResource($result->user), $result->tokenData());
    }
}
