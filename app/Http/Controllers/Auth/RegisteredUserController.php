<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'role' => ['required', 'in:seller,client'],
            'telephone' => ['required', 'string', 'max:20'], // Changed from 'phone_number' to 'telephone'
            'bio' => ['required', 'string', 'max:500'],
            'experience' => ['required', 'integer', 'min:0'],
            'profile_photo' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        $userData = [
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'telephone' => $request->telephone, // Changed from 'phone_number' to 'telephone'
            'bio' => $request->bio,
            'experience' => $request->experience,
        ];

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $userData['profile_photo_path'] = $path;
        }

        $user = User::create($userData);

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