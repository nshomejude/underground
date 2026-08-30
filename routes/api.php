<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Interfaces\Http\Api\V1\Controllers\CapabilityController;
use Interfaces\Http\Api\V1\Controllers\EngagementModelController;
use Interfaces\Http\Api\V1\Controllers\MembershipController;
use Interfaces\Http\Api\V1\Controllers\MetricController;
use Interfaces\Http\Api\V1\Controllers\PillarController;
use Interfaces\Http\Api\V1\Controllers\SectorController;

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
|
| The public contract of the platform. The HTML landing page is a client of
| these endpoints, not a parallel implementation of them.
|
| WORK IN PROGRESS: the version handshake, the Content endpoints, and the
| Membership endpoints are wired up so far. The resource endpoints below
| are the agreed shape — see README.md.
|
|   GET  /api/v1/landing-page
|   GET  /api/v1/capabilities            GET /api/v1/capabilities/{slug}
|   GET  /api/v1/sectors                 GET /api/v1/sectors/{slug}
|   GET  /api/v1/metrics
|   GET  /api/v1/engagement-models
|   GET  /api/v1/pillars
|   GET  /api/v1/insights                GET /api/v1/insights/{slug}
|   POST /api/v1/inquiries               GET /api/v1/inquiries/{reference}
|   GET  /api/v1/membership/tiers
|   POST /api/v1/membership/applications GET /api/v1/membership/applications/{reference}
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

    Route::get('/capabilities', [CapabilityController::class, 'index'])->name('capabilities.index');
    Route::get('/capabilities/{slug}', [CapabilityController::class, 'show'])->name('capabilities.show');

    Route::get('/sectors', [SectorController::class, 'index'])->name('sectors.index');
    Route::get('/sectors/{slug}', [SectorController::class, 'show'])->name('sectors.show');

    Route::get('/metrics', [MetricController::class, 'index'])->name('metrics.index');

    Route::get('/engagement-models', [EngagementModelController::class, 'index'])->name('engagement-models.index');

    Route::get('/pillars', [PillarController::class, 'index'])->name('pillars.index');

    Route::prefix('membership')->name('membership.')->group(function (): void {
        Route::get('/tiers', [MembershipController::class, 'tiers'])->name('tiers');
        Route::post('/applications', [MembershipController::class, 'apply'])
            ->middleware('throttle:10,1')
            ->name('applications.store');
        Route::get('/applications/{reference}', [MembershipController::class, 'show'])->name('applications.show');
    });
});
