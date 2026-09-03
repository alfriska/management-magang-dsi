<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

/**
 * Controller to handle 2FA verification for email/password login.
 * This is separate from OAuth 2FA flow (SocialAuthController).
 */
class Email2FAController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA verification page for email login.
     */
    public function showVerifyForm(Request $request)
    {
        // Check if user has pending 2FA verification
        if (!$request->session()->has('2fa:user:id') || !$request->session()->has('2fa:from:email_login')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Silakan login terlebih dahulu.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            $request->session()->forget(['2fa:user:id', '2fa:from:email_login']);
            return redirect()->route('login')->withErrors([
                'email' => 'User tidak ditemukan.',
            ]);
        }

        return view('auth.email-2fa-verify', [
            'user' => $user,
        ]);
    }

    /**
     * Handle 2FA verification for email login.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Kode autentikator harus diisi.',
            'code.digits' => 'Kode autentikator harus 6 digit.',
        ]);

        // Verify session
        if (!$request->session()->has('2fa:user:id') || !$request->session()->has('2fa:from:email_login')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Session expired. Silakan login lagi.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            $request->session()->forget(['2fa:user:id', '2fa:from:email_login']);
            return redirect()->route('login')->withErrors([
                'email' => 'User tidak ditemukan.',
            ]);
        }

        // Check if user is pending
        if ($user->isIntern() && $user->intern && $user->intern->status === 'pending') {
            $request->session()->forget(['2fa:user:id', '2fa:from:email_login']);
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda masih menunggu persetujuan admin. Silakan tunggu konfirmasi.',
            ]);
        }

        if ($user->isPembimbing() && $user->supervisor && $user->supervisor->status === 'pending') {
            $request->session()->forget(['2fa:user:id', '2fa:from:email_login']);
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda masih menunggu persetujuan admin. Silakan tunggu konfirmasi.',
            ]);
        }

        // Get 2FA secret
        $secret = $user->google2fa_secret;

        if (!$secret) {
            $request->session()->forget(['2fa:user:id', '2fa:from:email_login']);
            return redirect()->route('login')->withErrors([
                'email' => '2FA tidak dikonfigurasi dengan benar. Silakan hubungi admin.',
            ]);
        }

        // Verify the OTP code with tolerance
        $valid = $this->google2fa->verifyKey($secret, $request->code, 4); // 4 windows = 2 minutes tolerance

        if (!$valid) {
            return back()->withErrors([
                'code' => 'Kode autentikator tidak valid. Pastikan kode sudah benar.',
            ]);
        }

        // Clear 2FA session data
        $request->session()->forget(['2fa:user:id', '2fa:from:email_login']);

        // Login the user
        Auth::login($user, $request->session()->has('login_remember'));
        $request->session()->regenerate();

        // Mark 2FA as verified for this session
        $request->session()->put('2fa:verified', $user->id);

        return redirect()->intended(route('dashboard'))->with('success', 
            'Login berhasil! Selamat datang, ' . $user->name
        );
    }

    /**
     * Cancel 2FA and return to login.
     */
    public function cancel(Request $request)
    {
        $request->session()->forget(['2fa:user:id', '2fa:from:email_login', 'login_remember']);
        return redirect()->route('login')->with('info', 'Login dibatalkan.');
    }
}
