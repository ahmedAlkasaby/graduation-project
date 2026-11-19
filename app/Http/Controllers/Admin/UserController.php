<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserTypeEnum;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Managing every kind of user (donor, volunteer, admin, super admin) from the
 * Blade admin panel, the twin of Api\V1\Admin\UserController. The same
 * UserService decides who may manage whom: nobody can manage or hand out a
 * type that outranks their own, and roles are only kept for the admin type.
 */
class UserController extends AdminController
{
    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
        $this->setClass('user');
    }

    /** The accepted filters are listed in UserFilterRequest. */
    public function index(UserFilterRequest $request): View
    {
        $filters = $request->validated();

        $users = $this->userService->manageableQuery($this->admin())
            ->with('roles')
            ->filter($filters)
            ->latest()
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return $this->listView('admin.users.index', $users, 'users', [
            'typeOptions' => ['all' => __('All')] + $this->typeOptions(),
            'trashedOptions' => ['' => __('Not deleted'), 'with' => __('With deleted'), 'only' => __('Only deleted')],
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formData());
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        abort_unless($this->userService->canAssignType($this->admin(), $data['type']), Response::HTTP_FORBIDDEN);

        $this->userService->create($data, $request);

        return redirect()->route('admin.users.index')->with('success', __('Created successfully'));
    }

    public function show(User $user): View
    {
        abort_unless($this->userService->canManage($this->admin(), $user), Response::HTTP_FORBIDDEN);

        return view('admin.users.show', ['user' => $user->load('roles')]);
    }

    public function edit(User $user): View
    {
        abort_unless($this->userService->canManage($this->admin(), $user), Response::HTTP_FORBIDDEN);

        return view('admin.users.edit', ['user' => $user->load('roles')] + $this->formData());
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $actor = $this->admin();
        $data = $request->validated();

        abort_unless($this->userService->canManage($actor, $user), Response::HTTP_FORBIDDEN);
        abort_unless($this->userService->canAssignType($actor, $data['type']), Response::HTTP_FORBIDDEN);

        if ($actor->is($user) && $data['type'] !== $user->type) {
            return back()->withInput()->with('error', __('You cannot change your own type'));
        }

        $this->userService->update($user, $data, $request);

        return redirect()->route('admin.users.index')->with('success', __('Updated successfully'));
    }

    /** Flips the user between active and inactive. */
    public function status(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $actor = $this->admin();

        abort_unless($this->userService->canManage($actor, $user), Response::HTTP_FORBIDDEN);

        if ($actor->is($user)) {
            return $this->refuse($request, __('You cannot deactivate your own account'), (bool) $user->active);
        }

        $user = $this->userService->toggleStatus($user);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'active' => (bool) $user->active]);
        }

        return back()->with('success', __('Updated successfully'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $actor = $this->admin();

        abort_unless($this->userService->canManage($actor, $user), Response::HTTP_FORBIDDEN);

        if ($actor->is($user)) {
            return redirect()->route('admin.users.index')->with('error', __('You cannot delete your own account'));
        }

        $this->userService->delete($user);

        return redirect()->route('admin.users.index')->with('success', __('Deleted successfully'));
    }

    public function restore(User $user): RedirectResponse
    {
        abort_unless($this->userService->canManage($this->admin(), $user), Response::HTTP_FORBIDDEN);

        $user->restore();

        return redirect()->route('admin.users.index')->with('success', __('Restored successfully'));
    }

    /**
     * The select lists the create / edit form needs, from the same
     * UserService::formOptions() the API serves, as `value => label`.
     *
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        $options = $this->userService->formOptions($this->admin());

        return [
            'typeOptions' => array_column($options['types'], 'label', 'value'),
            'roleOptions' => array_column(
                array_map(fn (array $role): array => $role + ['label' => $role['display_name'] ?: $role['name']], $options['roles']),
                'label',
                'id',
            ),
            'genderOptions' => ['' => __('None')] + array_column($options['genders'], 'label', 'value'),
            'localeOptions' => array_column($options['locales'], 'label', 'value'),
            'rolesForTypes' => $options['roles_for_types'],
            'emailRequiredFor' => $options['email_required_for'],
        ];
    }

    /** @return array<string, string> */
    private function typeOptions(): array
    {
        $types = $this->userService->manageableTypes($this->admin());

        return UserTypeEnum::optionsOnly($types);
    }

    private function refuse(Request $request, string $message, bool $active): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'active' => $active, 'message' => $message], Response::HTTP_FORBIDDEN);
        }

        return back()->with('error', $message);
    }
}
