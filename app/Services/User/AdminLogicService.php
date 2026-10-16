<?php

namespace App\Services\User;

use App\Models\User;
use App\Traits\AdminHelperTrait;

/**
 * The rules about who may create or edit whom, kept out of the controller so
 * they read as one thing and a project can change them in one place.
 */
class AdminLogicService
{
    use AdminHelperTrait;

    /**
     * Whether $actor is allowed to act on a user of the given type. Nobody can
     * hand out a type that outranks their own.
     */
    public function authorize(?string $type, ?User $actor): bool
    {
        if (! $actor || ! in_array($type, $this->user_roles_array)) {
            return false;
        }

        if (in_array($actor->type, $this->access_all_type)) {
            return true;
        }

        if (in_array($type, $this->access_all_type)) {
            return false;
        }

        if (in_array($type, $this->admin_roles) && ! in_array($actor->type, $this->admin_roles)) {
            return false;
        }

        return true;
    }

    /**
     * `type` is the single source of truth; is_admin exists so lists can be
     * filtered without a join. Everyone created in the panel is an admin —
     * the API is what creates the app's own users.
     */
    public function mapTypeFlags(array &$data): void
    {
        if (! isset($data['type'])) {
            return;
        }

        $data['is_admin'] = 1;
    }
}
