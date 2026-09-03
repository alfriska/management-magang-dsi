<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This middleware ensures that users who are in the middle of 2FA verification
     * cannot access protected routes without completing the 2FA process.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated, let the auth middleware handle it
        if (!$request->user()) {
            return $next($request);
        }

        $user = $request->user();

        // If 2FA is enabled, ensure user has completed 2FA verification in this session
        if ($user->google2fa_enabled) {
            // Check if 2FA was verified in this session
            if (!$request->session()->has('2fa:verified') || 
                $request->session()->get('2fa:verified') !== $user->id) {
                
                // Store user ID for verification
                $request->session()->put('2fa:user:id', $user->id);
                $request->session()->put('2fa:from:email_login', true);
                
                // Logout user to force 2FA verification
                auth()->logout();
                
                return redirect()->route('email.2fa.verify');
            }
        }

        return $next($request);
    }
}

