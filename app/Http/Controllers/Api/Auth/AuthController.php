<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Api\Auth\CheckUserRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\LogoutRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\Auth\LoginService;
use App\Services\Api\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

/**
 * Registering, signing in and signing out from the app.
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
    ) {
        parent::__construct();
    }


    public function checkUser(CheckUserRequest $request): JsonResponse
    {
        return $this->successResponse();
    }


    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->validated();

        $this->registerService->register($input);

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

        if ($token && isset($token->id)) {
            $user->tokens()->whereKey($token->id)->delete();
        }

        return $this->successResponse();
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
