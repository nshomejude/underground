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
 * Aborts 403 for a logged-in non-admin. Pair with the `auth` middleware,
 * which handles redirecting guests to /login — this middleware assumes an
 * authenticated user is already present.
 */
final class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Gate::allows('admin'), 403);

        return $next($request);
    }
}
