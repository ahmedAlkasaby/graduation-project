<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Managing every kind of user (donor, volunteer, admin, super admin) from the
 * dashboard. Roles are only kept for the admin type — see UserService.
 *
 * Access is decided by the route name: "admin.users.index" needs the
 * "users.index" permission, and so on (see AutoCheckPermission). On top of
 * that, nobody can manage or hand out a type that outranks their own.
 */
class UserController extends ApiHomeController
{
    public function __construct(private UserService $userService)
    {
        parent::__construct();
    }

    /** The accepted filters are listed in UserFilterRequest. */
    public function index(UserFilterRequest $request): JsonResponse
    {
        $query = $this->userService->manageableQuery($request->user())
            ->with('roles')
            ->filter($request->validated())
            ->latest();

        [$users, $paginated] = $this->getDataWithPagination($query, $request);

        $permissions = $this->listPagePermissions('users', [...self::LIST_PAGE_ACTIONS, 'is_restore' => 'restore']);

        return $this->collectionResponse(new UserCollection($users), ['permissions' => $permissions], null, $paginated);
    }

    /** The select lists and field rules the create / edit form needs. */
    public function formOptions(Request $request): JsonResponse
    {
        return $this->successResponse($this->userService->formOptions($request->user()));
    }

    public function store(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        abort_unless($this->userService->canAssignType($request->user(), $data['type']), Response::HTTP_FORBIDDEN);

        $user = $this->userService->create($data, $request);

        return $this->successResponse(['user' => new UserResource($user->load('roles'))], 'Success', Response::HTTP_CREATED);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        abort_unless($this->userService->canManage($request->user(), $user), Response::HTTP_FORBIDDEN);

        return $this->successResponse(['user' => new UserResource($user->load('roles'))]);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $actor = $request->user();
        $data = $request->validated();

        abort_unless($this->userService->canManage($actor, $user), Response::HTTP_FORBIDDEN);
        abort_unless($this->userService->canAssignType($actor, $data['type']), Response::HTTP_FORBIDDEN);
        abort_if($actor->is($user) && $data['type'] !== $user->type, Response::HTTP_FORBIDDEN, __('You cannot change your own type'));

        $user = $this->userService->update($user, $data, $request);

        return $this->successResponse(['user' => new UserResource($user->load('roles'))]);
    }

    /** Flips the user between active and inactive. */
    public function status(Request $request, User $user): JsonResponse
    {
        abort_unless($this->userService->canManage($request->user(), $user), Response::HTTP_FORBIDDEN);
        abort_if($request->user()->is($user), Response::HTTP_FORBIDDEN, __('You cannot deactivate your own account'));

        $user = $this->userService->toggleStatus($user);

        return $this->successResponse(['user' => new UserResource($user->load('roles'))]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        abort_unless($this->userService->canManage($request->user(), $user), Response::HTTP_FORBIDDEN);
        abort_if($request->user()->is($user), Response::HTTP_FORBIDDEN, __('You cannot delete your own account'));

        $this->userService->delete($user);

        return $this->successResponse(null, __('Deleted successfully'));
    }

    public function restore(Request $request, User $user): JsonResponse
    {
        abort_unless($this->userService->canManage($request->user(), $user), Response::HTTP_FORBIDDEN);

        $user->restore();

        return $this->successResponse(['user' => new UserResource($user->load('roles'))]);
    }
}
