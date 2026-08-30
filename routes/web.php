<?php

use App\Http\Controllers\CapabilityController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MembershipController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightController::class, 'show'])->name('insights.show');

Route::get('/capabilities/{slug}', [CapabilityController::class, 'show'])->name('capabilities.show');

Route::get('/confidential-inquiry', [InquiryController::class, 'create'])->name('inquiries.create');
Route::post('/confidential-inquiry', [InquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('inquiries.store');

Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
Route::get('/membership/apply/{tier}', [MembershipController::class, 'create'])->name('membership.apply');
Route::post('/membership/apply/{tier}', [MembershipController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('membership.store');
