<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhotoboxController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FrameController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    return redirect()->route('photobox.index');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/generate-token', [DashboardController::class, 'generateToken'])->name('generate-token');
        Route::post('/expire-token/{session}', [DashboardController::class, 'expireToken'])->name('expire-token');

        Route::resource('frames', FrameController::class);

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

// Photobox (User) Routes
Route::prefix('photobox')
    ->name('photobox.')
    ->group(function () {
        Route::get('/', [PhotoboxController::class, 'index'])->name('index');
        Route::post('/validate', [PhotoboxController::class, 'validateToken'])->name('validate');
        Route::get('/success', [PhotoboxController::class, 'success'])->name('success');

        // Routes that require a valid active photobox session token
        Route::middleware('photobox.session')->group(function () {
            Route::get('/select-frame', [PhotoboxController::class, 'selectFrame'])->name('select-frame');
            Route::post('/start', [PhotoboxController::class, 'startSession'])->name('start');
            Route::get('/session', [PhotoboxController::class, 'photoSession'])->name('session');
            Route::post('/save-collage', [PhotoboxController::class, 'saveCollage'])->name('save-collage');
            Route::get('/edit', [PhotoboxController::class, 'editCollage'])->name('edit');
            Route::post('/print', [PhotoboxController::class, 'print'])->name('print');
        });
    });
