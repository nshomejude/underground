<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $throttleKey = Str::transliterate(Str::lower($request->string('email')->value()).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Too many sign-in attempts. Please try again in '.max(1, (int) ceil($seconds / 60)).' minute(s).',
            ]);
        }

        $credentials = $request->only('email', 'password');

        // Two-factor accounts: verify the password WITHOUT logging in, then hand
        // off to the challenge. Only the user id, remember flag and a timestamp
        // are kept in the (regenerated) session.
        $provider = Auth::guard()->getProvider();
        $candidate = $provider->retrieveByCredentials($credentials);

        if ($candidate instanceof User && $candidate->hasTwoFactorEnabled() && $provider->validateCredentials($candidate, $credentials)) {
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            $request->session()->put(TwoFactorChallengeController::SESSION_KEY, [
                'id' => $candidate->getKey(),
                'remember' => $request->boolean('remember'),
                'at' => now()->getTimestamp(),
            ]);

            return redirect()->route('two-factor.challenge');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Those credentials do not match a member account.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return redirect()->intended(route('account.show'));
    }
}
