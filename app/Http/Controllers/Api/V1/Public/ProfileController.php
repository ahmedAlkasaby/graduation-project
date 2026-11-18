<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\UserAction;
use App\Http\Requests\Api\Profile\ChangeAddressRequest;
use App\Http\Requests\Api\Profile\ChangeAvailableRequest;
use App\Http\Requests\Api\Profile\ChangeImageRequest;
use App\Http\Requests\Api\Profile\ChangeLangRequest;
use App\Http\Requests\Api\Profile\ChangePasswordRequest;
use App\Http\Requests\Api\Profile\ChangeSettingsReuest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;

class ProfileController extends ApiHomeController
{
    public function __construct(
        protected UserAction $action
    ) {
        parent::__construct();
        $this->middleware('check.client', ['only' => ['delete', 'changeAddress']]);
    }

    public function index()
    {
        return $this->show();
    }

    public function store()
    {
        return $this->successResponse();
    }

    public function show()
    {
        $data = ['user' => new UserResource($this->authUser())];
        $data += $this->getSettingApi();

        return $this->successResponse($data);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->authUser();
        $primaryLogin = settings()->get('primary_login');
        try {
            $input = $request->validated();
            $user = $this->action->executeUpdateUser($user, $input, $primaryLogin);

            return $this->successResponse([
                'user' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return $this->failedResponse($e);
        }
    }

    public function delete()
    {
        $user = $this->authUser();

        try {
            $this->action->executeDeleteUser($user, $user->id);

            return $this->successResponse();
        } catch (\Exception $e) {
            return $this->errorResponse();
        }
    }

    public function destroy($id)
    {
        return $this->delete();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $this->authUser();

        try {
            $this->action->executeChangePassword($user, $request->password_old, $request->password);

            return $this->successResponse(
                null,
                $this->getMessageSuccess('password')
            );
        } catch (\Exception $e) {
            return $this->failedResponse($e);
        }
    }

    public function changeLang(ChangeLangRequest $request)
    {
        $user = $this->authUser();

        try {
            $user->update(['lang' => $request->lang]);

            return $this->successResponse(null, $this->getMessageSuccess('locale'));
        } catch (\Exception $e) {
            return $this->errorResponse($this->getMessageError());
        }
    }

    public function settings(ChangeSettingsReuest $request)
    {

        $user = $this->authUser();
        try {
            $user->update($request->validated());

            return $this->successResponse(null, $this->getMessageSuccess('profile'));
        } catch (\Exception $e) {
            return $this->errorResponse($this->getMessageError());
        }
    }

    public function changeAvailable(ChangeAvailableRequest $request)
    {

        $user = $this->authUser();
        try {
            $user->update($request->validated());

            return $this->successResponse($this->getMessageSuccess('available'));
        } catch (\Exception $e) {
            $message = $this->getMessageError();

            return $this->errorResponse($message);
        }
    }

    public function changeAddress(ChangeAddressRequest $request)
    {
        $user = $this->authUser();
        try {
            $this->action->executeChangeAddress($user, (int) $request->address_id);

            return $this->successResponse(
                null,
                $this->getMessageSuccess('address')
            );
        } catch (\Exception $e) {
            $message = $this->getMessageError('address');

            return $this->errorResponse($message);
        }
    }

    public function changeImage(ChangeImageRequest $request)
    {
        $user = $this->authUser();
        try {
            $this->action->executeChangeImage($user, $request->file('image'));
            $message = $this->getMessageSuccess('image');

            return $this->successResponse(null, $message);
        } catch (\Exception $e) {
            $message = $this->getMessageError('image');
            return $this->errorResponse($message);
        }
    }
}
