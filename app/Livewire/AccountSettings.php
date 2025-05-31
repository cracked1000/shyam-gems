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

class AccountSettings extends Component
{
    public $user;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $code;
    public $secret;
    public $qrCodeSvg;

    protected $rules = [
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
        'code' => 'required|string',
        'secret' => 'required|string',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user) {
            abort(403, 'Unauthorized access');
        }
        if (!$this->user->google2fa_enabled) {
            $this->generateSecret();
        }
    }

    public function generateSecret()
    {
        $google2fa = new Google2FA();
        $this->secret = $google2fa->generateSecretKey();
        $this->user->google2fa_secret = $this->secret;
        $this->user->save();

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
    }

    public function updatePassword()
    {
        $this->validate(['current_password', 'new_password']);

        if (!Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $this->user->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('message', 'Password updated successfully!');
    }

    public function enable2FA()
    {
        $this->validate(['code', 'secret']);

        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey($this->secret, $this->code)) {
            $this->addError('code', 'Invalid code. Please try again.');
            return;
        }

        $this->user->update(['google2fa_enabled' => true]);
        $this->reset(['code', 'secret', 'qrCodeSvg']);
        session()->flash('message', 'Two-Factor Authentication enabled successfully!');
    }

    public function disable2FA()
    {
        $this->user->update(['google2fa_enabled' => false, 'google2fa_secret' => null]);
        $this->reset(['secret', 'qrCodeSvg']);
        session()->flash('message', 'Two-Factor Authentication disabled successfully!');
    }

    public function render()
    {
        return view('livewire.account-settings')
            ->layout('components.layouts.app');
    }
}