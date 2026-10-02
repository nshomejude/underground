<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\MembershipPlanAdminController;
use App\Http\Controllers\Admin\PlanRequestAdminController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Plans routes (owned by the Plans workstream; required from routes/web.php)
|--------------------------------------------------------------------------
*/

// Public marketing comparison (read-only).
Route::get('/membership/plans', [PlanController::class, 'publicIndex'])->name('plans.public');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/account/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/account/plans/requests', [PlanRequestController::class, 'index'])->name('plans.requests');

    Route::middleware(['member.approved', 'throttle:5,60'])->group(function (): void {
        Route::post('/account/plans/request', [PlanRequestController::class, 'store'])->name('plans.request');
    });

    Route::delete('/account/plans/requests/{planChangeRequest}', [PlanRequestController::class, 'withdraw'])->name('plans.requests.withdraw');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::resource('plans', MembershipPlanAdminController::class)->except(['show']);

    Route::get('plan-requests', [PlanRequestAdminController::class, 'index'])->name('plan-requests.index');
    Route::get('plan-requests/{planChangeRequest}', [PlanRequestAdminController::class, 'show'])->name('plan-requests.show');
    Route::post('plan-requests/{planChangeRequest}/approve', [PlanRequestAdminController::class, 'approve'])->name('plan-requests.approve');
    Route::post('plan-requests/{planChangeRequest}/decline', [PlanRequestAdminController::class, 'decline'])->name('plan-requests.decline');
});
