<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Admin\ApplicationReviewController;
use App\Http\Controllers\Admin\InquiryReviewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CapabilityController;
use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/partners', [PartnerController::class, 'index'])->name('partners');
Route::get('/collaboration', [CollaborationController::class, 'index'])->name('collaboration');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/events', [EventController::class, 'index'])->name('events');

Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightController::class, 'show'])->name('insights.show');

Route::get('/capabilities/{slug}', [CapabilityController::class, 'show'])->name('capabilities.show');

Route::get('/confidential-inquiry', [InquiryController::class, 'create'])->name('inquiries.create');
Route::post('/confidential-inquiry', [InquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('inquiries.store');

Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
Route::get('/membership/cards', [MembershipController::class, 'cards'])->name('membership.cards');
Route::get('/membership/apply/{tier}', [MembershipController::class, 'create'])->name('membership.apply');
Route::post('/membership/apply/{tier}', [MembershipController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('membership.store');

// Member account area: standard Laravel session auth, hand-rolled on core
// framework primitives (Auth/Hash facades, session) — no Breeze/Fortify/
// Jetstream. See App\Http\Controllers\Auth and App\Http\Controllers\AccountController.
Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

Route::get('/account', [AccountController::class, 'show'])
    ->middleware('auth')
    ->name('account.show');

// Staff admin review queue: membership applications and confidential
// inquiries. `admin` implies a logged-in user (see EnsureUserIsAdmin's
// doc block); `auth` is still applied explicitly so a guest is redirected
// to /login rather than refused outright.
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function (): void {
    Route::get('/applications', [ApplicationReviewController::class, 'index'])->name('applications.index');
    Route::post('/applications/{reference}/approve', [ApplicationReviewController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{reference}/decline', [ApplicationReviewController::class, 'decline'])->name('applications.decline');

    Route::get('/inquiries', [InquiryReviewController::class, 'index'])->name('inquiries.index');
    Route::post('/inquiries/{reference}/transition', [InquiryReviewController::class, 'transition'])->name('inquiries.transition');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/account/settings', [AccountSettingsController::class, 'edit'])->name('account.settings');
    Route::post('/account/settings', [AccountSettingsController::class, 'update'])->name('account.settings.update');
    Route::post('/account/settings/password', [AccountSettingsController::class, 'updatePassword'])->name('account.settings.password');
});
