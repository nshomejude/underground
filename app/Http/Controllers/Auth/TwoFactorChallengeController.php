<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorChangedNotification;
use App\Support\TwoFactor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Second step of sign-in. The login controller stores only the pending user
 * id, the remember flag and a timestamp in the session; nothing is
 * authenticated until a valid TOTP or recovery code is presented here.
 */
final class TwoFactorChallengeController extends Controller
{
    public const SESSION_KEY = 'two_factor.pending';

    private const TTL_SECONDS = 600;

    public function create(Request $request): View|RedirectResponse
    {
        if ($this->pendingUser($request) === null) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);
        if ($user === null) {
            return redirect()->route('login')->with('error', 'Your sign-in session expired. Please log in again.');
        }

        $request->validate([
            'code' => ['nullable', 'string', 'max:20'],
            'recovery_code' => ['nullable', 'string', 'max:30'],
        ]);

        $throttleKey = 'two-factor|'.$user->getKey().'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = max(1, RateLimiter::availableIn($throttleKey));

            throw ValidationException::withMessages([
                'code' => 'Too many attempts. Please wait '.$seconds.' seconds before trying again.',
            ]);
        }

        $recovery = trim((string) $request->input('recovery_code', ''));
        $code = trim((string) $request->input('code', ''));
        $usedRecovery = false;

        if ($recovery !== '') {
            $ok = TwoFactor::consumeRecoveryCode($user, $recovery);
            $usedRecovery = $ok;
        } else {
            $ok = $code !== '' && TwoFactor::verifyCode($user, $code);
        }

        if (! $ok) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                $recovery !== '' ? 'recovery_code' : 'code' => 'That code is not valid.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $remember = (bool) ($request->session()->get(self::SESSION_KEY)['remember'] ?? false);
        $request->session()->forget(self::SESSION_KEY);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($usedRecovery) {
            $user->notify(new TwoFactorChangedNotification('recovery_used'));
        }

        return redirect()->intended(route('account.show'));
    }

    private function pendingUser(Request $request): ?User
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (! is_array($pending) || ! isset($pending['id'], $pending['at']) || (now()->getTimestamp() - (int) $pending['at']) > self::TTL_SECONDS) {
            $request->session()->forget(self::SESSION_KEY);

            return null;
        }

        $user = User::query()->find($pending['id']);

        return $user !== null && $user->hasTwoFactorEnabled() ? $user : null;
    }
}
