<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input)
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'bio' => ['nullable', 'string'],
            'experience' => ['nullable', 'string'],
            'role' => ['required', 'in:client,seller'],
            'password' => $this->passwordRules(),
        ])->validate();

        $profilePhotoPath = null;
        if (isset($input['profile_photo'])) {
            $profilePhotoPath = $input['profile_photo']->store('profile_photos', 'public');
        }

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'telephone' => $input['telephone'] ?? null,
            'profile_photo_path' => $profilePhotoPath,
            'bio' => $input['bio'] ?? null,
            'experience' => $input['experience'] ?? null,
            'role' => $input['role'],
            'password' => Hash::make($input['password']),
        ]);
    }
}