<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\GalleryPhotoAdminController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Team restructure routes (owned by the Team workstream; NOT required
| from the shared routes/web.php — see this file's integration note)
|--------------------------------------------------------------------------
|
| routes/web.php already defines GET /team -> TeamController::index as
| route name "team" (not "team.index" — that name does not exist yet).
| That route is intentionally left alone here; this file only adds the
| three new Team pages below plus the admin CRUD for the new Gallery
| content type.
|
| team.case-studies is also the intended redirect target for the old
| GET /portfolio route (still defined, unchanged, in routes/web.php) —
| another workstream is wiring that redirect. Once both this file and
| that redirect are required from routes/web.php, /portfolio should
| permanently redirect to route('team.case-studies').
*/

Route::get('/team/senior-leadership', [TeamController::class, 'seniorLeadership'])->name('team.senior-leadership');
Route::get('/team/gallery', [TeamController::class, 'gallery'])->name('team.gallery');
Route::get('/team/case-studies', [TeamController::class, 'caseStudies'])->name('team.case-studies');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'two-factor.admin'])->group(function (): void {
    Route::resource('gallery', GalleryPhotoAdminController::class)
        ->except(['show'])
        ->parameters(['gallery' => 'gallery_photo']);
});
