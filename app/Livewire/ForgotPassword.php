<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ForgotPassword extends Component
{
    public $username = '';
    public $message = '';
    public $status;

    protected $rules = [
        'username' => 'required|string|exists:users,username',
    ];

    protected $messages = [
        'username.required' => 'Please enter your username.',
        'username.exists' => 'No account found with that username.',
    ];

    public function submit()
    {
        $this->validate();

        try {
            // Find the user by username
            $user = User::where('username', $this->username)->first();

            if (!$user) {
                Log::warning('ForgotPassword: User not found for username', ['username' => $this->username]);
                $this->dispatch('show-error', message: 'No account found with that username.');
                return;
            }

            // Log the email we're sending to
            Log::info('ForgotPassword: Attempting to send reset link', ['email' => $user->email]);

            // Send the password reset link to the user's email
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('ForgotPassword: Reset link sent successfully', ['email' => $user->email]);
                $this->message = 'A password reset link has been sent to your email.';
                $this->username = '';
                $this->dispatch('show-success', message: 'Reset link sent! Check your email.');
            } else {
                Log::error('ForgotPassword: Failed to send reset link', [
                    'email' => $user->email,
                    'status' => $status,
                ]);
                $this->dispatch('show-error', message: 'Failed to send reset link. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('ForgotPassword: Error sending password reset link', [
                'username' => $this->username,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('show-error', message: 'An error occurred. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.forgot-password')
            ->layout('components.layouts.app')
            ->title('Forgot Password');
    }
}