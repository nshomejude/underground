<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Lot 1 redirects (owned by the Navigation/Footer workstream; NOT required
| from routes/web.php yet — integration wires this in, see that module's
| final report for the exact filename and route names).
|--------------------------------------------------------------------------
|
| Individual, purpose-built redirects for URLs that moved under the new
| information architecture. Deliberately not a blanket "send everything to
| one page" redirect — each old URL still resolves to its own destination.
|
| Integration note: every redirect below only takes effect once either
| (a) this file is required before the legacy route it replaces, in
| routes/web.php, or (b) that legacy route definition is removed by the
| module that superseded it — otherwise the earlier-registered route in
| web.php wins the match first (Laravel matches in registration order).
| Today routes/web.php still defines a real 'portfolio' route at this same
| '/portfolio' URI, and 'insights.index'/'insights.show' at '/insights' and
| '/insights/{slug}' — all three need to be removed (or this file placed
| ahead of them) for these redirects to actually fire.
*/

// Case Studies lives under Team in the new IA; team.case-studies is owned by
// the Team workstream (see this module's final report) and may not be
// merged yet, so resolution happens lazily inside the closure rather than
// at route-registration time.
Route::get('/portfolio', static fn () => redirect()->route('team.case-studies', [], 301))
    ->name('redirects.portfolio');

// Insights -> Reports Hub. The slug carries over unchanged (confirmed with
// the Reports Hub workstream's brief), so one parameterized redirect covers
// every article — including ones added after this file was written —
// without sending them all to a single landing page. Known slugs at the
// time of writing: discretion-as-strategy, infrastructure-as-influence,
// government-affairs-multipolar-world, geopolitical-realignment-capital-flows.
Route::permanentRedirect('/insights', '/reports-hub')
    ->name('redirects.insights.index');

Route::get('/insights/{slug}', static fn (string $slug) => redirect("/reports-hub/{$slug}", 301))
    ->name('redirects.insights.show');
