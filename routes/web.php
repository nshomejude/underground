<?php

use App\Http\Controllers\CapabilityController;
use App\Http\Controllers\InsightController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightController::class, 'show'])->name('insights.show');

Route::get('/capabilities/{slug}', [CapabilityController::class, 'show'])->name('capabilities.show');
