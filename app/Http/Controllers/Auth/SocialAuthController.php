<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Intern;
use App\Models\Supervisor;
use App\Models\Notification;
use App\Notifications\NewInternRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class SocialAuthController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal autentikasi dengan Google. Silakan coba lagi.',
            ]);
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        $isNewUser = false;

        if ($user) {
            // Update Google info
            $user->update([
                'google_id' => $googleUser->getId(),
                'provider' => 'google',
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Create new user (role will be set after profile completion)
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'provider' => 'google',
                'avatar' => $googleUser->getAvatar(),
                'role' => 'intern', // Default, will be updated after profile completion
                'password' => null,
            ]);

            $isNewUser = true;
        }

        // Store user info in session for next steps
        $request->session()->put('oauth_user_id', $user->id);
        $request->session()->put('oauth_authenticated', true);
        $request->session()->put('oauth_is_new', $isNewUser);

        // Check if user already has completed profile (has intern or supervisor record)
        $hasProfile = ($user->intern && $user->intern->school !== 'Belum diisi') || $user->supervisor;

        // Check if user has pending status
        if ($user->isIntern() && $user->intern && $user->intern->status === 'pending') {
            if ($user->google2fa_secret && $user->two_factor_confirmed_at) {
                return redirect()->route('oauth.pending');
            }
        }

        if ($user->isPembimbing() && $user->supervisor && $user->supervisor->status === 'pending') {
            if ($user->google2fa_secret && $user->two_factor_confirmed_at) {
                return redirect()->route('oauth.pending');
            }
        }

        // Check approved users
        if ($hasProfile) {
            $isApproved = false;
            if ($user->isIntern() && $user->intern && $user->intern->status === 'active') {
                $isApproved = true;
            }
            if ($user->isPembimbing() && $user->supervisor && $user->supervisor->status === 'active') {
                $isApproved = true;
            }
            if ($user->isAdmin()) {
                $isApproved = true;
            }

            if ($isApproved) {
                // User is approved, check 2FA
                if ($user->google2fa_secret && $user->two_factor_confirmed_at) {
                    return redirect()->route('oauth.2fa.verify');
                } else {
                    // Need to setup 2FA first - save plain text to google2fa_secret
                    if (!$user->google2fa_secret) {
                        $secret = $this->google2fa->generateSecretKey();
                        $user->update(['google2fa_secret' => $secret]);
                    }
                    $request->session()->put('oauth_after_2fa', 'login');
                    return redirect()->route('oauth.2fa.setup');
                }
            }
        }

        // New user or user without profile - setup 2FA first, then complete profile
        if (!$user->google2fa_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update(['google2fa_secret' => $secret]);
        }

        if ($user->two_factor_confirmed_at) {
            // 2FA already setup, go to profile completion
            return redirect()->route('oauth.complete-profile');
        } else {
            // Need to setup 2FA first
            $request->session()->put('oauth_after_2fa', 'complete-profile');
            return redirect()->route('oauth.2fa.setup');
        }
    }

    /**
     * Show 2FA setup page for OAuth users
     */
    public function show2FASetup(Request $request)
    {
        if (!$request->session()->has('oauth_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Silakan login lagi.']);
        }

        $user = User::find($request->session()->get('oauth_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User tidak ditemukan.']);
        }

        // Generate secret if not exists (Use google2fa_secret for plain text storage)
        if (!$user->google2fa_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update([
                'google2fa_secret' => $secret,
                'two_factor_secret' => null // Clear conflict
            ]); 
        } else {
            $secret = $user->google2fa_secret;
        }
        
        // Final clean
        $secret = strtoupper(str_replace(' ', '', $secret));

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'InternHub'),
            $user->email,
            $secret
        );

        $qrCodeSvg = $this->generateQrCode($qrCodeUrl);

        $afterAction = $request->session()->get('oauth_after_2fa', 'complete-profile');

        return view('auth.oauth-2fa-setup', [
            'user' => $user,
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
            'isPending' => false,
            'afterAction' => $afterAction,
        ]);
    }

    /**
     * Verify 2FA setup for OAuth users
     */
    public function verify2FASetup(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        if (!$request->session()->has('oauth_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired.']);
        }

        $user = User::find($request->session()->get('oauth_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User tidak ditemukan.']);
        }

        // Get secret from dedicated non-encrypted column
        $secret = $user->google2fa_secret;
        
        // Sanitize
        $secret = strtoupper(str_replace(' ', '', $secret));
        
        // Final clean check
        if (!$secret || strlen($secret) < 16) {
             return redirect()->route('oauth.2fa.setup')
                ->withErrors(['code' => 'Kunci keamanan tidak valid. Silakan reload halaman setup.']);
        }


        // DEBUG: Check secret and code in log
        \Illuminate\Support\Facades\Log::info('2FA Verify Setup Debug:', [
            'user_id' => $user->id, 
            'secret_len' => strlen($secret),
            'first_5' => substr($secret, 0, 5),
            'input_code' => $request->code,
            'source' => $user->google2fa_secret ? 'google2fa_secret' : 'two_factor_secret'
        ]);

        // Verify with window
        $valid = $this->google2fa->verifyKey($secret, $request->code, 8);

        if (!$valid) {
             return back()->withErrors(['code' => 'Kode salah. Pastikan jam di HP Anda sinkron.']);
        }

        // Generate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
        })->all();

        // Save 2FA data - Only use google2fa_secret (plain text)
        // Do NOT use two_factor_secret - Fortify trait will try to decrypt it
        $user->forceFill([
            'google2fa_enabled' => true,
            'google2fa_secret' => $secret, // Plain text for our custom OAuth flow
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        // Check what to do after 2FA
        $afterAction = $request->session()->get('oauth_after_2fa', 'complete-profile');

        if ($afterAction === 'login') {
            // User already has profile and is approved, login directly
            $request->session()->forget(['oauth_user_id', 'oauth_authenticated', 'oauth_is_new', 'oauth_after_2fa']);
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
        }

        // Redirect to complete profile
        return redirect()->route('oauth.complete-profile')->with('success', '2FA berhasil diaktifkan! Silakan lengkapi profil Anda.');
    }

    /**
     * Show complete profile page
     */
    public function showCompleteProfile(Request $request)
    {
        if (!$request->session()->has('oauth_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Silakan login lagi.']);
        }

        $user = User::find($request->session()->get('oauth_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User tidak ditemukan.']);
        }

        // Get active supervisors for intern to choose
        $supervisors = User::where('role', 'pembimbing')
            ->whereHas('supervisor', function ($query) {
                $query->where('status', 'active');
            })
            ->with('supervisor')
            ->orderBy('name')
            ->get();

        return view('auth.oauth-complete-profile', [
            'user' => $user,
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Submit complete profile
     */
    public function submitCompleteProfile(Request $request)
    {
        if (!$request->session()->has('oauth_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired.']);
        }

        $user = User::find($request->session()->get('oauth_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User tidak ditemukan.']);
        }

        $role = $request->input('role');

        // Validate based on role
        if ($role === 'intern') {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'school' => 'required|string|max:255',
                'nis' => 'required|string|max:50',
                'department' => 'required|string|max:255',
                'address' => 'required|string|max:1000',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'supervisor_id' => 'nullable|exists:users,id',
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'phone.required' => 'No. telepon wajib diisi.',
                'school.required' => 'Nama sekolah/universitas wajib diisi.',
                'department.required' => 'Jurusan wajib diisi.',
                'nis.required' => 'NISN/NIM wajib diisi.',
                'address.required' => 'Alamat lengkap wajib diisi.',
                'start_date.required' => 'Tanggal mulai wajib diisi.',
                'end_date.required' => 'Tanggal selesai wajib diisi.',
                'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
                'supervisor_id.exists' => 'Pembimbing yang dipilih tidak valid.',
            ]);

            // Update user
            $user->update([
                'name' => $request->name,
                'role' => 'intern',
            ]);

            // Create or update intern record
            Intern::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school' => $request->school,
                    'department' => $request->department,
                    'nis' => $request->nis,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'supervisor_id' => $request->supervisor_id,
                    'status' => 'pending',
                ]
            );

            // Notify admins
            $this->notifyAdmins($user, 'intern');

        } elseif ($role === 'pembimbing') {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'nip' => 'nullable|string|max:50',
                'institution' => 'required|string|max:255',
                'pembimbing_address' => 'nullable|string|max:1000',
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'phone.required' => 'No. telepon wajib diisi.',
                'institution.required' => 'Asal instansi wajib diisi.',
            ]);

            // Update user
            $user->update([
                'name' => $request->name,
                'role' => 'pembimbing',
            ]);

            // Create or update supervisor record
            Supervisor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $request->nip,
                    'institution' => $request->institution,
                    'address' => $request->pembimbing_address,
                    'phone' => $request->phone,
                    'status' => 'pending',
                ]
            );

            // Notify admins
            $this->notifyAdmins($user, 'pembimbing');
        }

        // Store email for pending page status check
        $request->session()->put('pending_user_email', $user->email);
        
        // Clear oauth session
        $request->session()->forget(['oauth_user_id', 'oauth_authenticated', 'oauth_is_new', 'oauth_after_2fa']);

        // Redirect to pending page
        return redirect()->route('oauth.pending')->with('success', 
            'Profil berhasil dilengkapi! Permohonan Anda sedang ditinjau oleh admin.'
        );
    }

    /**
     * Notify admins about new registration
     */
    private function notifyAdmins(User $user, string $type)
    {
        $admins = User::where('role', 'admin')->get();
        
        $title = $type === 'intern' ? 'Pendaftaran Magang Baru' : 'Pendaftaran Pembimbing Baru';
        $message = $user->name . ' telah mendaftar sebagai ' . ($type === 'intern' ? 'peserta magang' : 'pembimbing') . ' melalui Google OAuth.';
        $url = $type === 'intern' ? '/interns?status=pending' : '/supervisors?status=pending';

        foreach ($admins as $admin) {
            // Email notification
            try {
                if ($type === 'intern') {
                    $admin->notify(new NewInternRegistration($user));
                }
            } catch (\Exception $e) {
                // Ignore email errors
            }
            
            // In-app notification
            Notification::notify(
                $admin->id,
                'new_registration',
                $title,
                $message,
                $url,
                ['user_id' => $user->id, 'type' => $type]
            );
        }
    }

    /**
     * Show 2FA verification page for OAuth users (returning users)
     */
    public function show2FAVerify(Request $request)
    {
        if (!$request->session()->has('oauth_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired.']);
        }

        $user = User::find($request->session()->get('oauth_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User tidak ditemukan.']);
        }

        return view('auth.oauth-2fa-verify', ['user' => $user]);
    }

    /**
     * Verify 2FA code for OAuth users
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        if (!$request->session()->has('oauth_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired.']);
        }

        $user = User::find($request->session()->get('oauth_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User tidak ditemukan.']);
        }

        // Get secret - Only use google2fa_secret (Plain text)
        // Do NOT use two_factor_secret - it gets encrypted/decrypted by Fortify trait
        $secret = $user->google2fa_secret;

        // Sanitize
        $secret = $secret ? strtoupper(str_replace(' ', '', $secret)) : null;

        // Validate secret length
        if (!$secret || strlen($secret) < 16) {
             // Secret is broken/too short, reset
             $user->update([
                 'google2fa_secret' => null,
                 'google2fa_enabled' => false,
                 'two_factor_confirmed_at' => null
             ]);
             return redirect()->route('oauth.2fa.setup')
                ->withErrors(['code' => 'Kunci keamanan rusak. Silakan setup ulang 2FA.']);
        }


        // DEBUG: Check secret and code in log
        \Illuminate\Support\Facades\Log::info('2FA Verify Login Debug:', [
            'user_id' => $user->id, 
            'secret_len' => strlen($secret),
            'first_5' => substr($secret, 0, 5),
            'input_code' => $request->code,
            'source' => $user->google2fa_secret ? 'google2fa_secret' : 'two_factor_secret'
        ]);

        // Verify with bigger window (8 means +/- 4 minutes drift allowed)
        $valid = $this->google2fa->verifyKey($secret, $request->code, 8);

        if (!$valid) {
            return back()->withErrors(['code' => 'Kode salah atau kedaluwarsa.']);
        }

        // Clear session
        $request->session()->forget(['oauth_user_id', 'oauth_authenticated', 'oauth_is_new', 'oauth_after_2fa']);

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 
            'Login berhasil! Selamat datang, ' . $user->name
        );
    }

    /**
     * Show pending approval page
     */
    public function showPending(Request $request)
    {
        // Check if user email is stored in session or query param
        $email = $request->query('email') ?? $request->session()->get('pending_user_email');
        
        if ($email) {
            $request->session()->put('pending_user_email', $email);
        }
        
        return view('auth.oauth-pending');
    }

    /**
     * Check approval status via AJAX
     */
    public function checkApprovalStatus(Request $request)
    {
        $email = $request->input('email') ?? $request->session()->get('pending_user_email');
        
        if (!$email) {
            return response()->json([
                'approved' => false,
                'message' => 'Email tidak ditemukan'
            ]);
        }
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'approved' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        
        $isApproved = false;
        
        // Check intern status
        if ($user->isIntern() && $user->intern) {
            if ($user->intern->status === 'active') {
                $isApproved = true;
            }
        }
        
        // Check supervisor/pembimbing status
        if ($user->isPembimbing() && $user->supervisor) {
            if ($user->supervisor->status === 'active') {
                $isApproved = true;
            }
        }
        
        // Admin is always approved
        if ($user->isAdmin()) {
            $isApproved = true;
        }
        
        if ($isApproved) {
            // Store user id for auto-login
            $request->session()->put('auto_login_user_id', $user->id);
            
            // Get role label for display
            $roleLabel = match($user->role) {
                'admin' => 'Administrator',
                'pembimbing' => 'Pembimbing',
                'intern' => 'Peserta Magang',
                default => 'User'
            };
            
            return response()->json([
                'approved' => true,
                'redirect_url' => route('oauth.auto-login'),
                'message' => 'Akun Anda telah disetujui! Mengalihkan ke dashboard...',
                'user_name' => $user->name,
                'user_role' => $roleLabel
            ]);
        }
        
        return response()->json([
            'approved' => false,
            'redirect_url' => null,
            'message' => 'Masih menunggu persetujuan'
        ]);
    }

    /**
     * Auto login user after approval and redirect to dashboard
     */
    public function autoLogin(Request $request)
    {
        $userId = $request->session()->get('auto_login_user_id');
        $email = $request->session()->get('pending_user_email');
        
        // Try to find user
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } elseif ($email) {
            $user = User::where('email', $email)->first();
        }
        
        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi telah berakhir. Silakan login kembali.'
            ]);
        }
        
        // Check if user is approved
        $isApproved = false;
        if ($user->isIntern() && $user->intern && $user->intern->status === 'active') {
            $isApproved = true;
        }
        if ($user->isPembimbing() && $user->supervisor && $user->supervisor->status === 'active') {
            $isApproved = true;
        }
        if ($user->isAdmin()) {
            $isApproved = true;
        }
        
        if (!$isApproved) {
            return redirect()->route('oauth.pending')->with('error', 'Akun Anda belum disetujui.');
        }
        
        // Clear pending session data
        $request->session()->forget(['pending_user_email', 'auto_login_user_id']);
        
        // Login user
        Auth::login($user);
        $request->session()->regenerate();
        
        // Get appropriate welcome message based on role
        $roleMessage = match($user->role) {
            'admin' => 'Administrator',
            'pembimbing' => 'Pembimbing',
            'intern' => 'Peserta Magang',
            default => 'User'
        };
        
        // Redirect to dashboard
        return redirect()->route('dashboard')->with('success', 
            'Selamat datang, ' . $user->name . '! Anda telah masuk sebagai ' . $roleMessage . '.'
        );
    }

    /**
     * Cancel OAuth 2FA flow
     */
    public function cancel2FA(Request $request)
    {
        $request->session()->forget(['oauth_user_id', 'oauth_authenticated', 'oauth_is_new', 'oauth_after_2fa']);
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
}
