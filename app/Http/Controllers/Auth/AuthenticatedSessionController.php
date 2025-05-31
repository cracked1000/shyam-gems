<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            $user->markAsOnline();

            if ($user->google2fa_enabled) {
                $request->session()->put('2fa:user:id', $user->id);
                return redirect()->to(
                    URL::temporarySignedRoute('2fa.challenge', now()->addMinutes(10))
                );
            }

            $request->session()->put('2fa_passed', true); // Mark 2FA as passed if not enabled
            return redirect()->intended($user->getDashboardRouteName());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->markAsOffline();
        }

        Auth::logout();
        $request->session()->forget(['2fa_passed', '2fa:user:id']); // Clear 2FA session keys
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}