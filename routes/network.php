<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\NetworkAdminController;
use App\Http\Controllers\NetworkConnectionController;
use App\Http\Controllers\NetworkDirectoryController;
use App\Http\Controllers\NetworkMemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Networking routes: directory, matchmaking, member pages, connections
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'member.approved'])->group(function (): void {
    Route::get('/network', [NetworkDirectoryController::class, 'index'])->name('network.index');
    Route::get('/network/matches', [NetworkDirectoryController::class, 'matches'])->name('network.matches');

    Route::get('/account/connections', [NetworkConnectionController::class, 'index'])->name('network.connections');
    Route::post('/account/connections/{connection}/respond', [NetworkConnectionController::class, 'respond'])
        ->middleware('throttle:60,1')->name('network.respond');
    Route::post('/account/connections/{connection}/withdraw', [NetworkConnectionController::class, 'withdraw'])
        ->middleware('throttle:60,1')->name('network.withdraw');
    Route::post('/account/connections/{connection}/remove', [NetworkConnectionController::class, 'remove'])
        ->middleware('throttle:60,1')->name('network.remove');

    Route::post('/network/{slug}/connect', [NetworkConnectionController::class, 'connect'])
        ->middleware('throttle:30,1')->name('network.connect');
    Route::post('/network/{slug}/block', [NetworkConnectionController::class, 'block'])
        ->middleware('throttle:30,1')->name('network.block');
    Route::post('/network/{slug}/unblock', [NetworkConnectionController::class, 'unblock'])
        ->middleware('throttle:30,1')->name('network.unblock');

    Route::get('/network/{slug}', [NetworkMemberController::class, 'show'])->name('network.show');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::get('/network', [NetworkAdminController::class, 'index'])->name('network.index');
    Route::get('/network/{connection}', [NetworkAdminController::class, 'show'])->whereNumber('connection')->name('network.show');
});
