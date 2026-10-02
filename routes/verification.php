<?php

declare(strict_types=1);

use App\Http\Controllers\VerificationAdminController;
use App\Http\Controllers\VerificationCompanyController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\VerificationFileController;
use App\Http\Controllers\VerificationIdentityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Verification routes (owned by the Verification workstream; required from routes/web.php)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('account/verification')->group(function (): void {
    Route::get('/', [VerificationController::class, 'index'])->name('verification.index');

    Route::get('/files/{kind}/{id}/{slot}', [VerificationFileController::class, 'show'])
        ->whereIn('kind', ['identity', 'company'])->whereNumber('id')
        ->middleware('throttle:120,1')
        ->name('verification.file');

    Route::prefix('identity')->name('verification.identity.')->group(function (): void {
        Route::get('/', [VerificationIdentityController::class, 'show'])->name('show');
        Route::post('/start', [VerificationIdentityController::class, 'start'])->name('start');
        Route::post('/consent', [VerificationIdentityController::class, 'consent'])->name('consent');
        Route::post('/details', [VerificationIdentityController::class, 'details'])->name('details');
        Route::post('/files', [VerificationIdentityController::class, 'files'])->middleware('throttle:20,1')->name('files');
        Route::delete('/files/{slot}', [VerificationIdentityController::class, 'removeFile'])->name('files.remove');
        Route::post('/submit', [VerificationIdentityController::class, 'submit'])->middleware('throttle:10,1')->name('submit');
        Route::delete('/', [VerificationIdentityController::class, 'withdraw'])->name('withdraw');
    });

    Route::prefix('company')->name('verification.company.')->group(function (): void {
        Route::get('/', [VerificationCompanyController::class, 'show'])->name('show');
        Route::post('/start', [VerificationCompanyController::class, 'start'])->name('start');
        Route::post('/consent', [VerificationCompanyController::class, 'consent'])->name('consent');
        Route::post('/details', [VerificationCompanyController::class, 'details'])->name('details');
        Route::post('/documents', [VerificationCompanyController::class, 'documents'])->middleware('throttle:20,1')->name('documents');
        Route::delete('/documents/{slot}', [VerificationCompanyController::class, 'removeFile'])->name('documents.remove');
        Route::post('/submit', [VerificationCompanyController::class, 'submit'])->middleware('throttle:10,1')->name('submit');
        Route::delete('/', [VerificationCompanyController::class, 'withdraw'])->name('withdraw');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::prefix('verifications')->name('verifications.')->group(function (): void {
        Route::get('/', [VerificationAdminController::class, 'index'])->name('index');
        Route::get('/{kind}/{id}', [VerificationAdminController::class, 'show'])->whereIn('kind', ['identity', 'company'])->whereNumber('id')->name('show');
        Route::post('/{kind}/{id}/approve', [VerificationAdminController::class, 'approve'])->whereIn('kind', ['identity', 'company'])->whereNumber('id')->name('approve');
        Route::post('/{kind}/{id}/reject', [VerificationAdminController::class, 'reject'])->whereIn('kind', ['identity', 'company'])->whereNumber('id')->name('reject');
        Route::post('/{kind}/{id}/info', [VerificationAdminController::class, 'requestInfo'])->whereIn('kind', ['identity', 'company'])->whereNumber('id')->name('info');
        Route::post('/{kind}/{id}/notes', [VerificationAdminController::class, 'notes'])->whereIn('kind', ['identity', 'company'])->whereNumber('id')->name('notes');
    });
});
