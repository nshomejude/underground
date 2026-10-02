<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\MotionAdminController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VoteMotionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Voting routes (owned by the Voting workstream; required from routes/web.php)
|--------------------------------------------------------------------------
| Internal voting lives only inside the member dashboard. See
| design/network-architecture.md for the shared middleware and route names.
*/

Route::middleware(['auth', 'member.approved'])->prefix('account/votes')->name('votes.')->group(function (): void {
    Route::get('/', [VoteController::class, 'index'])->name('index');

    Route::get('/create', [VoteMotionController::class, 'create'])->name('create');
    Route::post('/create', [VoteMotionController::class, 'store'])->middleware('throttle:20,1')->name('store');

    Route::get('/{motion}', [VoteController::class, 'show'])->whereNumber('motion')->name('show');
    Route::post('/{motion}/cast', [VoteController::class, 'cast'])->whereNumber('motion')->middleware('throttle:30,1')->name('cast');

    Route::get('/{motion}/edit', [VoteMotionController::class, 'edit'])->whereNumber('motion')->name('edit');
    Route::put('/{motion}', [VoteMotionController::class, 'update'])->whereNumber('motion')->middleware('throttle:20,1')->name('update');
    Route::post('/{motion}/publish', [VoteMotionController::class, 'publish'])->whereNumber('motion')->middleware('throttle:20,1')->name('publish');
    Route::post('/{motion}/cancel', [VoteMotionController::class, 'cancel'])->whereNumber('motion')->middleware('throttle:20,1')->name('cancel');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::get('/motions', [MotionAdminController::class, 'index'])->name('motions.index');
    Route::get('/motions/create', [MotionAdminController::class, 'create'])->name('motions.create');
    Route::post('/motions', [MotionAdminController::class, 'store'])->name('motions.store');
    Route::get('/motions/export', [MotionAdminController::class, 'export'])->name('motions.export');
    Route::get('/motions/{motion}', [MotionAdminController::class, 'show'])->whereNumber('motion')->name('motions.show');
    Route::post('/motions/{motion}/cancel', [MotionAdminController::class, 'cancel'])->whereNumber('motion')->name('motions.cancel');
    Route::post('/motions/{motion}/close', [MotionAdminController::class, 'close'])->whereNumber('motion')->name('motions.close');
});
