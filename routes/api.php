<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
|
| The public contract of the platform. The HTML landing page is a client of
| these endpoints, not a parallel implementation of them.
|
| WORK IN PROGRESS: only the version handshake is wired up so far. The
| resource endpoints below are the agreed shape — see README.md.
|
|   GET  /api/v1/landing-page
|   GET  /api/v1/capabilities            GET /api/v1/capabilities/{slug}
|   GET  /api/v1/sectors                 GET /api/v1/sectors/{slug}
|   GET  /api/v1/metrics
|   GET  /api/v1/engagement-models
|   GET  /api/v1/pillars
|   GET  /api/v1/insights                GET /api/v1/insights/{slug}
|   POST /api/v1/inquiries               GET /api/v1/inquiries/{reference}
|
*/

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/', fn () => response()->json([
        'data' => [
            'service' => 'underground-network',
            'version' => 'v1',
            'status' => 'in-development',
        ],
    ]))->name('index');
});
