<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserTypeEnum;
use App\Http\Requests\Admin\LoginAdminRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Session sign in for the Blade admin panel. Only an active admin or super
 * admin gets in; donors and volunteers use the app.
 */
class LoginAdminController extends AdminController
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(LoginAdminRequest $request): RedirectResponse
    {
        $user = User::query()
            ->whereIn('type', UserTypeEnum::adminValues())
            ->where($request->loginColumn(), $request->input('email'))
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (! $user->active) {
            throw ValidationException::withMessages(['email' => __('Your account is not active')]);
        }

        if (! $user->canLoginWeb()) {
            throw ValidationException::withMessages(['email' => __('Your role cannot sign in to the web dashboard')]);
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
