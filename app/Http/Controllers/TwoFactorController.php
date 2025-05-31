<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function setup()
    {
        $user = Auth::user();

        if ($user->google2fa_enabled) {
            return view('2fa.setup');
        }

        $secretKey = $this->google2fa->generateSecretKey();
        $user->google2fa_secret = $secretKey;
        $user->save();

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('2fa.setup', compact('qrCodeSvg', 'secretKey'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'secret' => 'required|string',
        ]);

        $user = Auth::user();
        $valid = $this->google2fa->verifyKey($request->secret, $request->code);

        if (!$valid) {
            return redirect()->route('2fa.setup')->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->google2fa_enabled = true;
        $user->save();

        return redirect()->route('2fa.setup')->with('success', 'Two-Factor Authentication has been enabled.');
    }

    public function disable()
    {
        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return redirect()->route('2fa.setup')->with('success', 'Two-Factor Authentication has been disabled.');
    }

    public function challenge()
    {
        $user = Auth::user();
        if (!$user->google2fa_enabled || session('2fa_passed')) {
            return redirect()->route($user->getDashboardRouteName());
        }

        return view('2fa.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user->google2fa_enabled) {
            $request->session()->put('2fa_passed', true);
            return redirect()->route($user->getDashboardRouteName());
        }

        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return redirect()->route('2fa.challenge')->with('error', 'Invalid code. Please try again.');
        }

        $request->session()->put('2fa_passed', true);
        $request->session()->forget('2fa:user:id'); // Clear the temporary user ID

        return redirect()->intended(route($user->getDashboardRouteName()));
    }
}