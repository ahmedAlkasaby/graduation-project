<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Blade admin panel's door, the session twin of EnsureUserIsAdmin: a
 * guest goes to the login page, and a signed in user who is not an active
 * admin is signed out and sent back there with the reason.
 */
class AdminPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }

        if (! $user->isAdmin() || ! $user->active || ! $user->canLoginWeb()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match (true) {
                ! $user->isAdmin() => __("You don't have permission to do this"),
                ! $user->active => __('Your account is not active'),
                default => __('Your role cannot sign in to the web dashboard'),
            };

            return redirect()->route('admin.login')->with('error', $message);
        }

        return $next($request);
    }
}
