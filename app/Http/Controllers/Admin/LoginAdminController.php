<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\AdminHelperTrait;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAdminController extends Controller
{
    use AdminHelperTrait, AuthenticatesUsers;

    protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * The login field carries either an email or a phone number, so which
     * column is matched depends on what was typed.
     */
    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'phone';

        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            // Credentials were right, but the panel is not for everyone: a
            // disabled or non-admin account is signed straight back out.
            if (! $this->mayEnterPanel(Auth::user())) {
                Auth::logout();
                $request->session()->invalidate();

                return $this->sendFailedLoginResponse($request);
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }

    private function mayEnterPanel($user): bool
    {
        if (! $user || ! $user->isAdmin()) {
            return false;
        }

        return in_array($user->type, $this->access_all_type)
            || $user->isAbleTo($this->admin_perms_models);
    }
}
