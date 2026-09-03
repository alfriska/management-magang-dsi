<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA setup page with QR Code
     */
    public function showSetupForm(Request $request)
    {
        // Check if user has completed Google OAuth
        if (!$request->session()->has('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'error' => 'Silakan login dengan Google terlebih dahulu.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'error' => 'User tidak ditemukan.',
            ]);
        }

        // Generate secret if not exists
        if (!$user->google2fa_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update(['google2fa_secret' => $secret]);
        } else {
            $secret = $user->google2fa_secret;
        }

        // Generate QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'InternHub'),
            $user->email,
            $secret
        );

        $qrCodeSvg = $this->generateQrCode($qrCodeUrl);

        return view('auth.2fa.setup', [
            'user' => $user,
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    /**
     * Handle 2FA setup verification
     */
    public function setupVerify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        if (!$request->session()->has('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'error' => 'Session expired. Silakan login lagi.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'error' => 'User tidak ditemukan.',
            ]);
        }

        // Verify OTP
        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password
        );

        if (!$valid) {
            return back()->withErrors([
                'one_time_password' => 'Kode autentikator tidak valid. Pastikan kode sudah benar.',
            ]);
        }

        // Enable 2FA for user
        $user->update(['google2fa_enabled' => true]);

        // Clear 2FA session
        $request->session()->forget(['2fa:user:id', '2fa:user:authenticated']);

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 
            '2FA berhasil diaktifkan! Selamat datang, ' . $user->name
        );
    }

    /**
     * Show 2FA verification page
     */
    public function showVerifyForm(Request $request)
    {
        // Check if user has completed Google OAuth
        if (!$request->session()->has('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'error' => 'Silakan login dengan Google terlebih dahulu.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'error' => 'User tidak ditemukan.',
            ]);
        }

        return view('auth.2fa.verify', [
            'user' => $user,
        ]);
    }

    /**
     * Handle 2FA verification
     */
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        if (!$request->session()->has('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'error' => 'Session expired. Silakan login lagi.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'error' => 'User tidak ditemukan.',
            ]);
        }

        // Verify OTP
        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password,
            2 // Window of 2 (allows 60 seconds before and after)
        );

        if (!$valid) {
            return back()->withErrors([
                'one_time_password' => 'Kode autentikator tidak valid. Pastikan kode sudah benar.',
            ]);
        }

        // Clear 2FA session
        $request->session()->forget(['2fa:user:id', '2fa:user:authenticated']);

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 
            'Login berhasil! Selamat datang, ' . $user->name
        );
    }

    /**
     * Cancel 2FA process and return to login
     */
    public function cancel(Request $request)
    {
        $request->session()->forget(['2fa:user:id', '2fa:user:authenticated']);
        return redirect()->route('login')->with('info', 'Login dibatalkan.');
    }

    /**
     * Generate QR Code SVG
     */
    private function generateQrCode(string $url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        
        return $writer->writeString($url);
    }

    /**
     * Show 2FA management page for authenticated users
     */
    public function showManagement()
    {
        $user = Auth::user();
        return view('auth.2fa.manage', ['user' => $user]);
    }

    /**
     * Regenerate 2FA secret
     */
    public function regenerate(Request $request)
    {
        $user = Auth::user();
        
        // Generate new secret
        $secret = $this->google2fa->generateSecretKey();
        $user->update([
            'google2fa_secret' => $secret,
            'google2fa_enabled' => false,
        ]);

        // Generate QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'InternHub'),
            $user->email,
            $secret
        );

        $qrCodeSvg = $this->generateQrCode($qrCodeUrl);

        return view('auth.2fa.regenerate', [
            'user' => $user,
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    /**
     * Confirm regenerated 2FA
     */
    public function confirmRegenerate(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $user = Auth::user();

        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password
        );

        if (!$valid) {
            return back()->withErrors([
                'one_time_password' => 'Kode autentikator tidak valid.',
            ]);
        }

        $user->update(['google2fa_enabled' => true]);

        return redirect()->route('profile.show')->with('success', 
            '2FA berhasil diperbarui!'
        );
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
            'password' => 'required',
        ]);

        $user = Auth::user();

        // Verify OTP
        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password
        );

        if (!$valid) {
            return back()->withErrors([
                'one_time_password' => 'Kode autentikator tidak valid.',
            ]);
        }

        $user->update([
            'google2fa_secret' => null,
            'google2fa_enabled' => false,
        ]);

        return redirect()->route('profile.show')->with('success', 
            '2FA berhasil dinonaktifkan.'
        );
    }
}
