<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\TwoFactorChangedNotification;
use App\Support\TwoFactor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Member-side two-factor management: enrol, confirm, recovery codes, disable.
 * Every state change re-checks the account password in the request itself.
 */
final class TwoFactorController extends Controller
{
    private const SESSION_CODES = 'two_factor_pending_recovery_codes';

    public function start(Request $request): RedirectResponse
    {
        $user = $this->user($request);
        $this->assertPassword($request, $user, 'twoFactorStart');

        if ($user->hasTwoFactorEnabled()) {
            return $this->back()->with('status', 'Two-factor authentication is already enabled.');
        }

        TwoFactor::beginEnrolment($user);

        return $this->back();
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $this->user($request);

        if ($user->hasPendingTwoFactorSetup()) {
            TwoFactor::disable($user);
        }

        return $this->back();
    }

    public function confirm(Request $request): RedirectResponse
    {
        $user = $this->user($request);
        $request->validateWithBag('twoFactorConfirm', ['code' => ['required', 'string', 'max:20']], [], ['code' => 'authentication code']);

        if (! $user->hasPendingTwoFactorSetup() || ! TwoFactor::verifyCode($user, (string) $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'That code is not valid. Check your authenticator app and try again.',
            ])->errorBag('twoFactorConfirm');
        }

        TwoFactor::confirm($user);
        $codes = TwoFactor::regenerateRecoveryCodes($user);
        $request->session()->put(self::SESSION_CODES, $codes);
        $user->notify(new TwoFactorChangedNotification('enabled'));

        return $this->back()->with('status', 'Two-factor authentication is now enabled.');
    }

    public function regenerate(Request $request): RedirectResponse
    {
        $user = $this->user($request);
        $this->assertPassword($request, $user, 'twoFactorRegenerate');

        if (! $user->hasTwoFactorEnabled()) {
            return $this->back();
        }

        $request->session()->put(self::SESSION_CODES, TwoFactor::regenerateRecoveryCodes($user));
        $user->notify(new TwoFactorChangedNotification('regenerated'));

        return $this->back()->with('status', 'New recovery codes generated. Your previous codes no longer work.');
    }

    public function acknowledge(Request $request): RedirectResponse
    {
        $request->validate(['saved' => ['accepted']], ['saved.accepted' => 'Please confirm you have saved your recovery codes.']);
        $request->session()->forget(self::SESSION_CODES);

        return $this->back()->with('status', 'Recovery codes acknowledged. Keep them somewhere safe.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $this->user($request);
        $this->assertPassword($request, $user, 'twoFactorDisable');
        $request->validateWithBag('twoFactorDisable', ['code' => ['required', 'string', 'max:30']], [], ['code' => 'code']);

        if ($user->is_admin && config('auth.require_admin_two_factor')) {
            throw ValidationException::withMessages([
                'code' => 'Two-factor authentication is mandatory for staff administrators and cannot be disabled.',
            ])->errorBag('twoFactorDisable');
        }

        $code = (string) $request->input('code');
        $valid = $user->hasTwoFactorEnabled()
            && (TwoFactor::verifyCode($user, $code) || TwoFactor::consumeRecoveryCode($user, $code));

        if (! $valid) {
            throw ValidationException::withMessages([
                'code' => 'That code is not valid.',
            ])->errorBag('twoFactorDisable');
        }

        TwoFactor::disable($user);
        $request->session()->forget(self::SESSION_CODES);
        $user->notify(new TwoFactorChangedNotification('disabled'));

        return $this->back()->with('status', 'Two-factor authentication has been disabled.');
    }

    private function user(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        return $user;
    }

    private function back(): RedirectResponse
    {
        return redirect()->route('account.security');
    }

    private function assertPassword(Request $request, User $user, string $bag): void
    {
        $request->validateWithBag($bag, ['current_password' => ['required', 'string']], [], ['current_password' => 'password']);

        if (! Hash::check((string) $request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'That password does not match your current password.',
            ])->errorBag($bag);
        }
    }
}
