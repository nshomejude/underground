<?php

declare(strict_types=1);

use App\Http\Controllers\MembershipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Membership content pages (owned by the Membership content-pages
| workstream; required from routes/web.php)
|--------------------------------------------------------------------------
| Public, informational pages that explain what membership includes.
| These are content pages only — no paid logic, quota-tracking, usage
| metering, or billing lives here (that is separate, later work).
*/
Route::get('/membership/plus', [MembershipController::class, 'plus'])->name('membership.plus');
Route::get('/membership/events', [MembershipController::class, 'events'])->name('membership.events');
