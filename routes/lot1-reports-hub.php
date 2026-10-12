<?php

declare(strict_types=1);

use App\Http\Controllers\ReportsHubController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reports Hub: the renamed, richer-metadata successor to Insights.
|--------------------------------------------------------------------------
|
| This reuses the existing Insight aggregate (src/Domain/Insights, extended
| with author/country/sector/access-level metadata) rather than a parallel
| content type.
|
| IMPORTANT for integration -- this file defines /insights and
| /insights/{slug} again (as redirects). Laravel's RouteCollection keys
| routes by method+URI, so registering the same URI twice does not just
| "shadow" the earlier one in matching -- the second registration evicts
| the first everywhere, including from the name lookup table. Concretely:
| requiring this file from routes/web.php WITHOUT first deleting its
| existing `insights.index` / `insights.show` lines does not leave the old
| controller reachable as a fallback -- it deletes the `insights.index` /
| `insights.show` ROUTE NAMES outright (route('insights.index') then
| throws RouteNotFoundException), which breaks every page that renders
| <x-site-header>, <x-site-footer>, <x-mobile-drawer> or <x-mobile-tab-bar>
| (all four call route('insights.index')), plus welcome.blade.php and
| SitemapController. So: remove web.php's two `insights.*` lines AND add
| `require __DIR__.'/lot1-reports-hub.php';` in the SAME change, and in
| that same change repoint the four nav components, welcome.blade.php and
| SitemapController from insights.index/insights.show to
| reports-hub.index/reports-hub.show. I verified this end to end locally
| (temporarily wiring the require, removing the two old web.php lines, and
| repointing those six files) -- all 8 of this module's own route/redirect
| behaviours passed, and the only breakage sitewide was the now-superseded
| tests/Feature/Insights/InsightPagesTest.php (asserts the old insights.*
| routes exist -- retire it) and tests/Feature/Navigation/NavLinksTest.php
| (asserts an "Insights" nav label routed to insights.index -- repoint it
| to reports-hub.index). None of that was left wired in this commit; see
| the final report for the full list of follow-ups.
|
*/

Route::get('/reports-hub', [ReportsHubController::class, 'index'])->name('reports-hub.index');
Route::get('/reports-hub/strategic-notes', [ReportsHubController::class, 'strategicNotes'])->name('reports-hub.strategic-notes');
Route::get('/reports-hub/{slug}', [ReportsHubController::class, 'show'])->name('reports-hub.show');

// Individual redirects from the superseded Insights URLs. Each article
// keeps going to its own equivalent URL (slug preserved) -- not a blanket
// redirect to a single page.
Route::get('/insights', function () {
    return redirect()->route('reports-hub.index', [], 301);
})->name('reports-hub.redirects.insights-index');

Route::get('/insights/{slug}', function (string $slug) {
    return redirect()->route('reports-hub.show', ['slug' => $slug], 301);
})->name('reports-hub.redirects.insights-show');
