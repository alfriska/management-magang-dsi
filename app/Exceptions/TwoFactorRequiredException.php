<?php

namespace App\Exceptions;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

/**
 * Exception thrown when user requires 2FA verification during login.
 * This exception is caught by the Handler and redirects to 2FA verification page.
 */
class TwoFactorRequiredException extends Exception
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        parent::__construct('Two-factor authentication required.');
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request)
    {
        return redirect()->route('email.2fa.verify');
    }
}
