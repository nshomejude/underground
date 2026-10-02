<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * The member's Security page: sign-in and recovery status, plus the
 * password-change and account-deletion forms (handled by
 * AccountSettingsController).
 */
final class AccountSecurityController extends Controller
{
    public function __invoke(): View
    {
        return view('account.security');
    }
}
