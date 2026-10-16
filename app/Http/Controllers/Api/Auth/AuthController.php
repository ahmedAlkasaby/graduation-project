<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Exceptions\UserFacingException;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Api\Auth\CheckUserRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\LogoutRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\Auth\LoginService;
use App\Services\Api\Auth\RegisterService;
use App\Services\Api\Auth\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Registering, signing in, refreshing tokens and signing out from the app.
 *
 * Everything with a rule in it lives in a request or a service: the request
 * decides what a valid payload is, RegisterService decides what registering
 * means, LoginService decides what counts as a successful sign in. What is left
 * here is choosing a response.
 */
class AuthController extends ApiHomeController
{
    public function __construct(
        private readonly LoginService $loginService,
        private readonly RegisterService $registerService,
        private readonly TokenService $tokenService,
    ) {
        parent::__construct();
    }

    /**
     * Confirm the email is free to register and send it the verification code.
     */
    public function checkUser(CheckUserRequest $request): JsonResponse
    {
        $this->registerService->sendVerificationCode(
            $request->validated('email'),
            $request->validated('phone'),
            $this->sms_minutes,
        );

        return $this->successResponse();
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->validated();

        try {
            $this->registerService->register($input);
        } catch (UserFacingException $e) {
            return $this->failedResponse($e);
        }

        return $this->respondWithLogin($input);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->respondWithLogin($request->validated());
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        $user = $this->authUser();

        $user->devices()->where('imei', $request->validated('imei'))->delete();

        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $this->tokenService->revoke($token);
        }

        return $this->successResponse();
    }

    /**
     * Trade the refresh token sent as the bearer token for a new access /
     * refresh pair. The refresh token used here stops working.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        $result = $this->tokenService->refresh($user, $user->currentAccessToken());

        return $this->resourceResponse(new UserResource($result->user), $result->tokenData());
    }

    private function respondWithLogin(array $input): JsonResponse
    {
        $credentials = $this->loginService->getCredentials(
            $input['email'] ?? $input['phone'] ?? null,
            $input['password'],
        );

        $result = $this->loginService->attemptLogin(
            $credentials,
            $input['imei'],
            $input['token'],
            $input['device_type'],
        );

        if (! $result instanceof AuthenticatedUserDTO) {
            return $this->errorResponse($this->getMessageError('unauthorise'), 401);
        }

        return $this->resourceResponse(new UserResource($result->user), $result->tokenData());
    }
}
