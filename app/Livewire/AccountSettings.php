<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Validation\Rules\Password;

class AccountSettings extends Component
{
    public $user;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $code;
    public $secret;
    public $qrCodeSvg;
    public $showQrCode = false;
    public $isLoading = false;

    protected function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'code' => 'required|string|size:6',
        ];
    }

    protected $messages = [
        'current_password.required' => 'Please enter your current password.',
        'new_password.required' => 'Please enter a new password.',
        'new_password.confirmed' => 'The password confirmation does not match.',
        'code.required' => 'Please enter the 6-digit code from your authenticator app.',
        'code.size' => 'The code must be exactly 6 digits.',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        
        if (!$this->user) {
            abort(403, 'Unauthorized access');
        }
    }

    public function generateSecret()
    {
        try {
            $this->isLoading = true;
            
            $google2fa = new Google2FA();
            $this->secret = $google2fa->generateSecretKey();
            
            // Store the secret temporarily (don't save to DB until 2FA is enabled)
            session(['temp_2fa_secret' => $this->secret]);

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $this->user->email,
                $this->secret
            );

            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            
            $writer = new Writer($renderer);
            $this->qrCodeSvg = $writer->writeString($qrCodeUrl);
            $this->showQrCode = true;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate QR code. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function updatePassword()
    {
        try {
            $this->isLoading = true;
            
            // Validate password fields
            $this->validate([
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
            ]);

            // Check if current password is correct
            if (!Hash::check($this->current_password, $this->user->password)) {
                $this->addError('current_password', 'The current password is incorrect.');
                return;
            }

            // Check if new password is different from current
            if (Hash::check($this->new_password, $this->user->password)) {
                $this->addError('new_password', 'The new password must be different from your current password.');
                return;
            }

            // Update password
            $this->user->update([
                'password' => Hash::make($this->new_password)
            ]);

            // Clear form fields
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
            
            session()->flash('message', 'Password updated successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so they display properly
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update password. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function enable2FA()
    {
        try {
            $this->isLoading = true;
            
            $this->validate([
                'code' => 'required|string|size:6',
            ]);

            $secret = session('temp_2fa_secret') ?? $this->secret;
            
            if (!$secret) {
                $this->addError('code', 'No 2FA secret found. Please generate a new QR code.');
                return;
            }

            $google2fa = new Google2FA();
            
            // Remove any spaces or special characters from the code
            $cleanCode = preg_replace('/[^0-9]/', '', $this->code);
            
            if (!$google2fa->verifyKey($secret, $cleanCode)) {
                $this->addError('code', 'Invalid code. Please check your authenticator app and try again.');
                return;
            }

            // Save the secret and enable 2FA
            $this->user->update([
                'google2fa_enabled' => true,
                'google2fa_secret' => $secret
            ]);

            // Clear temporary data
            session()->forget('temp_2fa_secret');
            $this->reset(['code', 'secret', 'qrCodeSvg']);
            $this->showQrCode = false;
            
            session()->flash('message', 'Two-Factor Authentication enabled successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to enable 2FA. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function disable2FA()
    {
        try {
            $this->isLoading = true;
            
            $this->user->update([
                'google2fa_enabled' => false,
                'google2fa_secret' => null
            ]);

            $this->reset(['secret', 'qrCodeSvg', 'code']);
            $this->showQrCode = false;
            session()->forget('temp_2fa_secret');
            
            session()->flash('message', 'Two-Factor Authentication disabled successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to disable 2FA. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function cancelQrCode()
    {
        $this->reset(['secret', 'qrCodeSvg', 'code']);
        $this->showQrCode = false;
        session()->forget('temp_2fa_secret');
    }

    public function render()
    {
        return view('livewire.account-settings')
            ->layout('components.layouts.app');
    }
}