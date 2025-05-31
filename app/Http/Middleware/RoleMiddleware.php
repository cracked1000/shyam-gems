<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request with role-based access control
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'required_roles' => $roles,
                'requested_url' => $request->url(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);

            return $this->redirectToUserDashboard($user);
        }

        return $next($request);
    }

    /**
     * Redirect user to their appropriate dashboard
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToUserDashboard($user)
    {
        $message = 'Access denied. You have been redirected to your dashboard.';

        if (!$user->role) {
            Log::error('User has no role assigned', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'requested_url' => request()->url(),
                'timestamp' => now(),
            ]);
            return redirect()->route('home')->with('error', 'Account error: No role assigned. Please contact support.');
        }

        switch ($user->role) {
            case 'seller':
                return redirect()->route('seller.dashboard')->with('error', $message);
            case 'client':
                return redirect()->route('client.dashboard')->with('error', $message);
            default:
                Log::warning('Unexpected user role', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'timestamp' => now(),
                ]);
                return redirect()->route('home')->with('error', 'Account error: Invalid role. Please contact support.');
        }
    }
}