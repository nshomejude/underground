<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileAvatarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Profiles routes (owned by the Profiles workstream; required from routes/web.php)
|--------------------------------------------------------------------------
| Any signed-in member may edit their own profile; the directory listing
| itself is gated elsewhere (approved membership + identity verification).
*/
Route::middleware('auth')->prefix('account/profile')->group(function (): void {
    Route::get('/', [ProfileController::class, 'show'])->name('account.profile');
    Route::put('/', [ProfileController::class, 'update'])->middleware('throttle:60,1')->name('account.profile.update');
    Route::post('/avatar', [ProfileAvatarController::class, 'store'])->middleware('throttle:20,1')->name('account.profile.avatar');
    Route::delete('/avatar', [ProfileAvatarController::class, 'destroy'])->middleware('throttle:20,1')->name('account.profile.avatar.destroy');
});
