<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\MediaItemAdminController;
use App\Http\Controllers\FlexMediaGroupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Flex Media Group: Livestream, Podcast, and Underground Magazine
|--------------------------------------------------------------------------
|
| Public listing page, plus admin CRUD over its content (mirrors the
| Events admin CRUD registered in routes/web.php). This file is required
| from routes/web.php during integration; it does not touch that file.
*/

Route::get('/flex-media-group', [FlexMediaGroupController::class, 'index'])->name('flex-media-group');

Route::prefix('admin')->middleware(['auth', 'admin', 'two-factor.admin'])->name('admin.')->group(function (): void {
    Route::resource('media-items', MediaItemAdminController::class)
        ->except(['show'])
        ->parameters(['media-items' => 'media_item']);
});
