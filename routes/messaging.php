<?php

declare(strict_types=1);

use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessagePollController;
use App\Http\Controllers\MessageReportController;
use App\Http\Controllers\MessageStoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Messaging routes (owned by the Messaging workstream; required from routes/web.php)
|--------------------------------------------------------------------------
| Private messages between the two parties of an accepted connection.
*/

Route::middleware(['auth', 'member.approved'])->prefix('account/messages')->name('messages.')->group(function (): void {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::get('/{conversation}', [MessageController::class, 'show'])->whereNumber('conversation')->name('show');
    Route::post('/{conversation}', MessageStoreController::class)->whereNumber('conversation')->middleware('throttle:30,1')->name('store');
    Route::get('/{conversation}/poll', MessagePollController::class)->whereNumber('conversation')->middleware('throttle:60,1')->name('poll');
    Route::post('/{conversation}/messages/{message}/report', MessageReportController::class)
        ->whereNumber(['conversation', 'message'])->middleware('throttle:20,1')->name('report');
});
