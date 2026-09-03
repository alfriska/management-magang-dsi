<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Custom views for Fortify
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            $supervisors = \App\Models\Supervisor::where('status', 'active')->with('user')->get();
            return view('auth.register', compact('supervisors'));
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', [
                'token' => $request->route('token'),
                'email' => $request->email ?? $request->query('email', ''),
            ]);
        });

        // Custom authentication for login with 2FA check
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // For OAuth users without password
                if ($user->provider && !$user->password) {
                    return null; // Force them to use Google OAuth
                }
                
                // For regular users with password
                if ($user->password && Hash::check($request->password, $user->password)) {
                    // Check if user is intern with pending status
                    if ($user->isIntern() && $user->intern && $user->intern->status === 'pending') {
                        return null; // Will show generic error, but prevents login
                    }

                    // Check if user is pembimbing with pending status
                    if ($user->isPembimbing() && $user->supervisor && $user->supervisor->status === 'pending') {
                        return null; // Will show generic error, but prevents login
                    }

                    // Check if user has Google 2FA enabled
                    if ($user->google2fa_enabled && $user->google2fa_secret) {
                        // Store user ID in session for 2FA verification
                        $request->session()->put('2fa:user:id', $user->id);
                        $request->session()->put('2fa:from:email_login', true);
                        $request->session()->put('login_remember', $request->boolean('remember'));
                        
                        // Return null to prevent auto-login, we'll redirect manually
                        // Use a custom exception to trigger 2FA redirect
                        throw new \App\Exceptions\TwoFactorRequiredException($user);
                    }
                    
                    return $user;
                }
            }

            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}

