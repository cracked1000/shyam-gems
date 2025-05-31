<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:seller,client'], // Add role validation
        ]);

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name), // Combine first_name and last_name for name
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Use the role from the form
        ]);

        event(new \Illuminate\Auth\Events\Registered($user));

        Auth::login($user);

        if ($user->role === 'seller') {
            return redirect()->route('seller.dashboard');
        } elseif ($user->role === 'client') {
            return redirect()->route('client.dashboard');
        }

        return redirect()->route('dashboard');
    }
}