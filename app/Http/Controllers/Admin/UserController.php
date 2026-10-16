<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\ImageHandlerService;
use App\Services\User\AdminLogicService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends AdminController
{
    protected ImageHandlerService $imageHandler;

    public function __construct(ImageHandlerService $imageHandler)
    {
        parent::__construct();

        $this->setClass('user');
        $this->imageHandler = $imageHandler;
    }

    public function index(Request $request)
    {
        return view('admin.users.index', [
            'data' => User::filter($request)->latest()->paginateRequested($request, $this->limit),
            'user_type' => 'all',
            'user_delete' => $this->checkPerm($this->table . '.destroy'),
            'route_create' => $this->checkPerm($this->table . '.create'),
            'route_edit' => $this->checkPerm($this->table . '.edit'),
        ]);
    }

    public function trash(Request $request)
    {
        return view('admin.users.index', [
            'data' => User::onlyTrashed()->latest()->paginateRequested($request, $this->limit),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.users.create', [
            'new' => 1,
            'roles' => $this->getRoles(),
        ]);
    }

    public function store(UserRequest $request, AdminLogicService $logic)
    {
        $input = $request->validated();

        if (! $logic->authorize($input['type'], $this->user)) {
            return $this->pageUnauthorized();
        }

        $input['image'] = $this->imageHandler->uploadImage('uploads/users', $request);
        $logic->mapTypeFlags($input);

        $user = User::create(Arr::except($input, ['roles']));

        if ($user->is_admin) {
            $user->syncRoles($this->allowedRoleNames($input['roles'] ?? []));
        }

        return redirect(
            $request->input('previous_url') ?: route('admin.users.index', ['type' => $input['type']])
        )->with('success', __('User created successfully'));
    }

    public function show(Request $request, $id, AdminLogicService $logic)
    {
        return $this->edit($id, $logic);
    }

    public function edit($id, AdminLogicService $logic)
    {
        if (! $this->mayTouch($id)) {
            return $this->pageError();
        }

        $user = User::find($id);

        if (! $user) {
            return $this->pageError();
        }

        if (! $logic->authorize($user->type, $this->user)) {
            return $this->pageUnauthorized();
        }

        return view('admin.users.edit', [
            'user' => $user,
            'new' => 0,
            'roles' => $this->getRoles(),
            'userRoles' => $user->roles->pluck('id')->toArray(),
        ]);
    }

    public function update(UserRequest $request, $id, AdminLogicService $logic)
    {
        if (! $this->mayTouch($id)) {
            return $this->pageError();
        }

        $user = User::find($id);

        if (! $user) {
            return $this->pageError();
        }

        if (! $logic->authorize($user->type, $this->user)) {
            return $this->pageUnauthorized();
        }

        $input = $request->validated();

        if (blank($input['password'] ?? null)) {
            unset($input['password']);
        }

        $input['image'] = $this->imageHandler->editImage($request, $user, 'uploads/users');
        $logic->mapTypeFlags($input);

        $user->update(Arr::except($input, ['roles']));

        if ($user->is_admin) {
            $user->syncRoles($this->allowedRoleNames($input['roles'] ?? []));
        }

        if ($this->user->id == $id && isset($input['locale'])) {
            app()->setLocale($input['locale']);
        }

        return redirect()
            ->route('admin.users.index', ['type' => $user->type])
            ->with('success', __('User updated successfully'));
    }

    public function destroy($id)
    {
        if (! $this->mayTouch($id)) {
            return $this->pageError();
        }

        User::findOrFail($id)->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted successfully'));
    }

    public function restore($id)
    {
        User::withTrashed()->where('id', $id)->restore();

        if (! User::find($id)) {
            return $this->pageError();
        }

        return redirect()->route('admin.users.trash')->with('success', __('User restore successfully'));
    }

    /**
     * The seeded owner accounts can only be touched by one of themselves.
     */
    private function mayTouch($id): bool
    {
        return ! in_array((int) $id, $this->access_all_id)
            || in_array($this->user->id, $this->access_all_id);
    }

    /**
     * Only roles the signed in user was offered can be assigned — the ids come
     * back from a form and a request can carry any id at all.
     */
    private function allowedRoleNames(array $roleIds): array
    {
        $allowed = array_keys($this->getRoles());

        return \App\Models\Role::whereIn('id', array_intersect($roleIds, $allowed))
            ->pluck('name')
            ->toArray();
    }
}
