<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResetPassword extends Component
{
    public $token = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $message = '';
    public $isSuccess = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    protected $messages = [
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'password.required' => 'Please enter a new password.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'password_confirmation.required' => 'Please confirm your password.',
    ];

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->input('email');
        Log::info('ResetPassword: Mounted with token', ['token' => $token, 'email' => $this->email]);
    }

    public function submit()
    {
        $this->validate();

        try {
            Log::info('ResetPassword: Attempting password reset', [
                'email' => $this->email,
                'token' => $this->token,
            ]);

            $status = Password::reset(
                $this->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                    Log::info('ResetPassword: Password updated for user', ['email' => $user->email]);
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                Log::info('ResetPassword: Password reset successful', ['email' => $this->email]);
                $this->isSuccess = true;
                $this->message = 'Password reset successfully! Redirecting to login...';
                
                // Use session flash message for the login page
                session()->flash('success', 'Password reset successful! You can now log in with your new password.');
                
                // Redirect directly using Laravel's redirect helper
                return redirect()->route('login');
            } else {
                Log::error('ResetPassword: Failed to reset password', [
                    'email' => $this->email,
                    'status' => $status,
                ]);
                $this->message = 'Invalid or expired token. Please request a new reset link.';
                $this->addError('general', 'Invalid or expired token. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('ResetPassword: Error resetting password', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
            $this->message = 'An error occurred. Please try again later.';
            $this->addError('general', 'An error occurred. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.reset-password')
            ->layout('components.layouts.app')
            ->title('Reset Password');
    }
}