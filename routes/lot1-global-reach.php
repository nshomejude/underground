<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\OfficeAdminController;
use App\Http\Controllers\GlobalAllianceController;
use App\Http\Controllers\LocationsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global Reach restructure: alliance model + office status
|--------------------------------------------------------------------------
|
| The existing GET /global-reach -> GlobalReachController route (name
| "global-reach") already lives in routes/web.php and is extended, not
| replaced, by this module — it is intentionally NOT redefined here.
| This file only adds the two new public pages plus the admin CRUD for
| the underlying Office directory.
*/

Route::get('/global-alliance', GlobalAllianceController::class)->name('global-alliance');
Route::get('/locations', LocationsController::class)->name('locations');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::resource('offices', OfficeAdminController::class)
        ->except(['show'])
        ->parameters(['offices' => 'office']);
});
