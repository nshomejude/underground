<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Staff administrators must have two-factor authentication enabled. Applied
 * to the admin route group only, so the enrolment, security and logout routes
 * stay reachable (no lockout loop). Toggle: config('auth.require_admin_two_factor').
 */
final class EnsureAdminHasTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->is_admin && config('auth.require_admin_two_factor') && ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('account.security')
                ->with('two_factor_required', 'Two-factor authentication is mandatory for staff administrators. Set it up below to continue to the admin area.');
        }

        return $next($request);
    }
}
