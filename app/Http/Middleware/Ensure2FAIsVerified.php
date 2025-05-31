<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class Ensure2FAIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->google2fa_enabled && !$request->session()->has('2fa_passed')) {
            // Ensure the user ID matches the session to prevent session hijacking
            if ($request->session()->get('2fa:user:id') !== $user->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Session mismatch. Please log in again.');
            }

            return redirect()->to(
                URL::temporarySignedRoute('2fa.challenge', now()->addMinutes(10))
            );
        }

        return $next($request);
    }
}