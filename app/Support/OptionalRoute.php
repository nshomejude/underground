<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Resolves a named route to its URL, falling back to a placeholder when the
 * name is not yet registered.
 *
 * Several site-wide navigation components (header, mobile drawer, footer)
 * link to route names owned by modules built in parallel elsewhere in this
 * project that may not be merged yet. Calling route() directly for a name
 * that does not exist throws and would take down every page that renders
 * the header. This keeps those components (and this module's own tests)
 * working before integration; once the real route lands, it starts
 * resolving automatically — no change needed here.
 */
final class OptionalRoute
{
    /** @param  array<int|string, mixed>  $parameters */
    public static function url(string $name, array $parameters = [], string $fallback = '#'): string
    {
        return Route::has($name) ? route($name, $parameters) : $fallback;
    }
}
