<?php

namespace App\Actions\User;

use App\Actions\Base\BaseAction;
use App\Exceptions\UserFacingException;
use App\Models\User;
use App\Services\User\ProfileService;
use App\Traits\AdminTrait;
use App\Traits\HelperTrait;
use App\Traits\ResponseMessageTrait;
use Illuminate\Support\Facades\Hash;

/**
 * The write side of the profile API, one method per thing a user can change.
 * Each throws with a message the controller turns into an error response.
 */
class UserAction extends BaseAction
{
    use AdminTrait, HelperTrait, ResponseMessageTrait;

    public function __construct(protected ProfileService $service) {}

    public function executeChangeImage(User $user, $imageFile): string
    {
        $imagePath = $this->uploadImage($imageFile, 'profiles');

        $user->update(['image' => $imagePath]);

        return $imagePath;
    }

    public function executeChangePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw new UserFacingException($this->getMessageError('password_old'));
        }

        if (Hash::check($newPassword, $user->password)) {
            throw new UserFacingException(__('The old password must not match the new password'));
        }

        $user->update(['password' => $newPassword]);
    }


    public function executeDeleteUser(User $authUser, int $id): void
    {
        if (in_array($id, $this->access_all_id) || in_array($authUser->id, $this->access_all_id)) {
            throw new UserFacingException(__('You are not allowed to delete this user'));
        }

        User::findOrFail($id)->delete();
    }

    public function executeUpdateUser(User $user, array $input, string $primaryLogin): User
    {
        if (! empty($input['is_phone'])) {
            if ($primaryLogin === 'phone') {
                throw new UserFacingException(__('Phone is the primary login and cannot be changed'));
            }

            return $this->service->updatePhone($user, $input['phone']);
        }

        return $this->service->updateProfile($user, $input, $primaryLogin);
    }
}
