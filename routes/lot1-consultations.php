<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ConsultationReviewController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationTrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Consultation Desk routes (owned by the Lot 1 Consultation Qualification
| workstream; required from routes/web.php)
|--------------------------------------------------------------------------
| The free, no-login qualification intake that replaces an unstructured
| exploratory call. Sibling to the confidential-inquiry routes defined in
| routes/web.php — this is the lighter-weight, no-cost lane; the paid
| booking/payment flow it feeds into is a later, separate lot.
*/

Route::get('/consultations', [ConsultationController::class, 'create'])->name('consultations.create');
Route::post('/consultations', [ConsultationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('consultations.store');

// Public, no-login status lookup by reference — see ConsultationTrackController.
Route::get('/consultations/track', [ConsultationTrackController::class, 'show'])->name('consultations.track');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::get('/consultations', [ConsultationReviewController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/{reference}', [ConsultationReviewController::class, 'show'])->name('consultations.show');
    Route::post('/consultations/{reference}/transition', [ConsultationReviewController::class, 'transition'])->name('consultations.transition');
});
