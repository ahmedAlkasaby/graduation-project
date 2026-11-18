<?php

namespace App\Services\User;

use App\Builders\UserBuilder;
use App\Enums\LanguageEnum;
use App\Enums\UserTypeEnum;
use App\Models\Role;
use App\Models\User;
use App\Services\ImageHandlerService;
use App\Traits\AdminHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Creates and edits users from the dashboard: the stored fields, the image,
 * and the roles. Only an "admin" user carries roles — every other type has
 * its roles removed, so a donor never ends up with dashboard permissions.
 */
class UserService
{
    use AdminHelperTrait;

    private const IMAGE_FOLDER = 'uploads/users';

    public function __construct(
        private ImageHandlerService $imageHandler,
        private AdminLogicService $adminLogic,
    ) {}

    /**
     * Whether $actor may manage $target at all: the target's type must not
     * outrank the actor, and the seeded owner accounts are off limits to
     * anyone but themselves.
     */
    public function canManage(User $actor, User $target): bool
    {
        if ($this->isOwner($target) && ! $this->isOwner($actor)) {
            return false;
        }

        return $this->adminLogic->canManageType($actor, $target->type);
    }

    /**
     * Every user $actor may manage, by the same rule as canManage(): types at
     * their rank or below, and the owner accounts only for an owner.
     */
    public function manageableQuery(User $actor): UserBuilder
    {
        return User::query()
            ->ofTypes($this->manageableTypes($actor))
            ->when(! $this->isOwner($actor), fn (UserBuilder $query) => $query->whereKeyNot($this->access_all_id));
    }

    /**
     * The user types $actor may see and hand out, in hierarchy order.
     *
     * @return list<UserTypeEnum>
     */
    public function manageableTypes(User $actor): array
    {
        return array_values(array_filter(
            UserTypeEnum::cases(),
            fn (UserTypeEnum $type): bool => $this->adminLogic->canManageType($actor, $type->value),
        ));
    }

    /** The seeded owner accounts, which only an owner may touch. */
    private function isOwner(User $user): bool
    {
        return in_array($user->id, $this->access_all_id, true);
    }

    /** Whether $actor may give a user the type $type. */
    public function canAssignType(User $actor, string $type): bool
    {
        return $this->adminLogic->canManageType($actor, $type);
    }

    /**
     * What the create / edit form needs, worked out for $actor: only the types
     * they may hand out, and which types show the roles and email fields.
     *
     * @return array{
     *     types: list<array{value: string, label: string}>,
     *     roles: list<array{id: int, name: string, display_name: string|null}>,
     *     genders: list<array{value: string, label: string}>,
     *     locales: list<array{value: string, label: string}>,
     *     roles_for_types: list<string>,
     *     email_required_for: list<string>
     * }
     */
    public function formOptions(User $actor): array
    {
        return [
            'types' => $this->asOptions(UserTypeEnum::optionsOnly($this->manageableTypes($actor))),
            'roles' => Role::query()
                ->orderBy('display_name')
                ->get(['id', 'name', 'display_name'])
                ->map(fn (Role $role): array => $role->only(['id', 'name', 'display_name']))
                ->all(),
            'genders' => $this->asOptions(['male' => __('Male'), 'female' => __('Female')]),
            'locales' => $this->asOptions(LanguageEnum::options()),
            'roles_for_types' => [UserTypeEnum::Admin->value],
            'email_required_for' => UserTypeEnum::adminValues(),
        ];
    }

    public function create(array $data, Request $request): User
    {
        $data['image'] = $this->imageHandler->uploadImage(self::IMAGE_FOLDER, $request);

        return DB::transaction(function () use ($data): User {
            $user = User::create(Arr::except($data, ['roles']));

            $this->syncRoles($user, $data);

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data  validated UserRequest data
     */
    public function update(User $user, array $data, Request $request): User
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['image'] = $this->imageHandler->editImage($request, $user, self::IMAGE_FOLDER);

        return DB::transaction(function () use ($user, $data): User {
            $user->update(Arr::except($data, ['roles']));

            $this->syncRoles($user, $data);

            return $user;
        });
    }

    public function toggleStatus(User $user): User
    {
        $user->update(['active' => ! $user->active]);

        if (! $user->active) {
            $user->tokens()->delete();
        }

        return $user;
    }

    /** Soft deletes the user and revokes every token they hold. */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->tokens()->delete();
            $user->delete();
        });
    }

    /**
     * `value => label` as a list the frontend can loop over in order.
     *
     * @param  array<string, string>  $options
     * @return list<array{value: string, label: string}>
     */
    private function asOptions(array $options): array
    {
        return array_map(
            fn (string $value, string $label): array => ['value' => $value, 'label' => $label],
            array_keys($options),
            $options,
        );
    }

    private function syncRoles(User $user, array $data): void
    {
        if ($user->type !== UserTypeEnum::Admin->value) {
            $user->syncRoles([]);

            return;
        }

        if (array_key_exists('roles', $data)) {
            $user->syncRoles($data['roles'] ?? []);
        }
    }
}
