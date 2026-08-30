<?php

declare(strict_types=1);

// STUB: a sibling module owns the real admin foundation, this may be
// superseded at merge time.

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aborts 403 for a logged-in non-admin. Relies on the `auth` middleware
 * running first to redirect guests to /login — this middleware only guards
 * the "logged in but not staff" case.
 */
final class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Gate::allows('admin'), 403);

        return $next($request);
    }
}
