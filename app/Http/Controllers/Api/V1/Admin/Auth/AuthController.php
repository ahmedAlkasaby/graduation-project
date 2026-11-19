<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\DTOs\AuthenticatedUserDTO;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\Auth\LoginService;
use Illuminate\Http\JsonResponse;

/**
 * Signing in to the dashboard. Same payload and token pair as the app login,
 * but only an active admin or super admin gets a token; anyone else gets the
 * same 401 as a wrong password, so the endpoint does not reveal who is an admin.
 *
 * Refreshing and signing out stay on the public endpoints, which work for any
 * signed in user.
 */
class AuthController extends ApiHomeController
{
    public function __construct(
        private readonly LoginService $loginService,
    ) {
        parent::__construct();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->validated();

        $credentials = $this->loginService->restrictToAdmins(
            $this->loginService->getCredentials($input['email'], $input['password']),
        );

        $result = $this->loginService->attemptLogin(
            $credentials,
            $input['imei'],
            $input['token'],
            $input['device_type'],
            toDashboard: true,
        );

        if (! $result instanceof AuthenticatedUserDTO) {
            return $this->errorResponse($this->getMessageError('unauthorise'), 401);
        }

        return $this->resourceResponse(new UserResource($result->user), $result->tokenData());
    }
}
