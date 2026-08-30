<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

/**
 * "Forgot your password?" — sends a reset link via the core Password
 * broker (Illuminate\Auth\Passwords\PasswordBroker). The confirmation
 * message is intentionally identical whether or not the email belongs to
 * an account, so the response never reveals which addresses are
 * registered (a standard defence against email enumeration).
 */
final class ForgotPasswordController extends Controller
{
    private const string GENERIC_STATUS = 'If an account exists for that email address, we have sent a link to reset your password.';

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->only('email'));

        return back()->with('status', self::GENERIC_STATUS);
    }
}
