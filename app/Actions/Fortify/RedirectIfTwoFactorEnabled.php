<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;

/**
 * Custom action to check if user has Google 2FA enabled
 * and redirect to 2FA challenge page if so.
 */
class RedirectIfTwoFactorEnabled extends AttemptToAuthenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);

        if (!$user) {
            $this->throwFailedAuthenticationException($request);
        }

        // Check if user has 2FA enabled using google2fa_secret
        if ($user instanceof User && $user->google2fa_enabled && $user->google2fa_secret) {
            // Store user ID in session for 2FA verification
            $request->session()->put('2fa:user:id', $user->id);
            $request->session()->put('2fa:from:email_login', true);
            
            // Redirect to our custom 2FA verification page
            return redirect()->route('email.2fa.verify');
        }

        return $next($request);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function validateCredentials($request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return $user;
        }

        return null;
    }
}
