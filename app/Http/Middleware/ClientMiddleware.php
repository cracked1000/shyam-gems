<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== 'client') {
            Log::warning('Non-client attempted to access client area', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'requested_url' => $request->url(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);

            if ($user->role === 'seller') {
                return redirect()->route('seller.dashboard')
                    ->with('error', 'Access denied. Only clients can access this area.');
            }

            return redirect()->route('home')
                ->with('error', 'Access denied. Please contact support.');
        }

        return $next($request);
    }
}