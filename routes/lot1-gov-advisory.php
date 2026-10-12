<?php

declare(strict_types=1);

use App\Http\Controllers\GovernmentAdvisoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Government Advisory route (owned by the Homepage/Government Advisory
| workstream; required from routes/web.php)
|--------------------------------------------------------------------------
| A single public, unauthenticated page for heads of state, ministers and
| cabinet offices. Linked from the homepage and from /capabilities.
*/
Route::get('/government-advisory', [GovernmentAdvisoryController::class, 'index'])->name('government-advisory');
