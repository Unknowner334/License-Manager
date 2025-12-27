<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebuiController;

Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login_action'])->name('login.post')->middleware('throttle:10,5');
    Route::post('/login/forgot', [AuthController::class, 'sendResetLink'])->name('login.forgot')->middleware('throttle:10,5');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'register_action'])->name('register.post')->middleware('throttle:10,5');
});

Route::prefix('API')->name('api.')->withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    Route::post('/connect', [ApiController::class, 'Authenticate'])->name('connect')->middleware('throttle:25,5');
});

Route::middleware('auth', 'session.timeout', 'no.cache')->group(function () {
    Route::get('/', [DashController::class, 'dashboard'])->name('dashboard');

    Route::prefix('API/private')->name('api.private.')->group(function () {
        Route::get('/home/licenses', [DashController::class, 'licensedata_10'])->name('home.registrations');
        Route::get('/apps/data', [AppController::class, 'appdata'])->name('apps.registrations');
        Route::post('/apps/register', [AppController::class, 'appregister'])->name('apps.register');
    });
});

Route::fallback(function () {return view('errors.fallback');});