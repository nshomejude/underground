<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Illuminate\Support\Facades\Route;

/**
 * routes/web.php is a shared file this module's dispatch is NOT allowed
 * to edit — the integrator wires `require __DIR__.'/lot1-team.php';` into
 * it separately. Until that happens, tests for this module register the
 * same file manually, under the same "web" middleware group Laravel's own
 * bootstrap/app.php applies when it loads web.php, so these tests
 * exercise the real, final route definitions.
 */
trait RegistersTeamRoutes
{
    protected function registerTeamRoutes(): void
    {
        Route::middleware('web')->group(function (): void {
            require base_path('routes/lot1-team.php');
        });

        // Route::has()/route() resolve names via a lookup table built once
        // and cached; routes added after the app has already booted (as
        // here) need it rebuilt explicitly or they stay invisible to both.
        Route::getRoutes()->refreshNameLookups();
    }
}
