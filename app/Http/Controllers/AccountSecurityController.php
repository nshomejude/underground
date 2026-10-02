<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Qr;
use App\Support\Totp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The member's Security page: sign-in and recovery status, two-factor
 * authentication, plus the password-change and account-deletion forms
 * (handled by AccountSettingsController / TwoFactorController).
 */
final class AccountSecurityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $setup = null;

        if ($user->hasPendingTwoFactorSetup() && ($secret = $user->twoFactorSecret()) !== null) {
            $issuer = (string) config('app.name', 'Underground Network');
            $setup = [
                'key' => trim(chunk_split($secret, 4, ' ')),
                'qr' => Qr::svg(Totp::uri($user->email, $secret, $issuer)),
            ];
        }

        return view('account.security', [
            'setup' => $setup,
            'recoveryCodes' => $request->session()->get('two_factor_pending_recovery_codes'),
            'twoFactorRequired' => (bool) config('auth.require_admin_two_factor') && $user->is_admin,
        ]);
    }
}
