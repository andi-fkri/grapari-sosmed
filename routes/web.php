<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\EnsureGrapariAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(EnsureGrapariAuthenticated::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/contents/fetch-link', [DashboardController::class, 'fetchLinkMetrics'])->name('contents.fetch-link');
    Route::post('/contents/sync-all', [DashboardController::class, 'syncAllMetrics'])->name('contents.sync-all');
    Route::post('/contents', [DashboardController::class, 'storeContent'])->name('contents.store');
    Route::post('/contents/{content}/sync', [DashboardController::class, 'syncContentMetrics'])->name('contents.sync');
    Route::delete('/contents/{content}', [DashboardController::class, 'destroyContent'])->name('contents.destroy');
    Route::post('/reset-demo', [DashboardController::class, 'resetDemo'])->name('demo.reset');
});
