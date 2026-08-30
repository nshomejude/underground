<?php

// STUB: a sibling module owns the real admin foundation, this may be
// superseded at merge time.

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backs the `admin` route middleware alias. Relies on `auth` being applied
 * alongside it on every protected route — that middleware redirects guests
 * to /login before this one ever runs, so by the time this executes the
 * user is guaranteed to be authenticated; a logged-in non-admin is refused
 * with a 403.
 */
final class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Gate::allows('admin'), 403);

        return $next($request);
    }
}
