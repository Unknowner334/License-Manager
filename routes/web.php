<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AppController;

// * Login
Route::get('/login', [AuthController::class, 'LoginView'])->name('login');
Route::post('/login', [AuthController::class, 'LoginPost'])->name('login.post')->middleware('throttle:10,5');
Route::post('/logout', [AuthController::class, 'Logout'])->name('logout')->middleware('throttle:10,5');

// * Register
Route::get('/register', [AuthController::class, 'RegisterView'])->name('register');
Route::post('/register', [AuthController::class, 'RegisterPost'])->name('register.post')->middleware('throttle:10,5');

// * Dashboard
Route::get('/', [DashController::class, 'Dashboard'])->middleware('auth');
Route::get('/dashboard', [DashController::class, 'Dashboard'])->name('dashboard')->middleware('auth');

// * Settings
Route::get('/settings', [SettingController::class, 'Settings'])->name('settings')->middleware('auth');
Route::post('/settings/username-change', [SettingController::class, 'SettingsUsername'])->name('settings.username')->middleware('auth');
Route::post('/settings/name-change', [SettingController::class, 'SettingsName'])->name('settings.name')->middleware('auth');
Route::post('/settings/password-change', [SettingController::class, 'SettingsPassword'])->name('settings.password')->middleware('auth');

// * Apps
Route::get('/apps', [AppController::class, 'AppListView'])->name('apps')->middleware('auth');
Route::get('/apps/{id}', [AppController::class, 'AppEditView'])->where('id', '[0-9a-fA-F-]{36}')->name('apps.edit')->middleware('auth');
Route::get('/apps/generate', [AppController::class, 'AppGenerateView'])->name('apps.generate')->middleware('auth');
Route::post('/apps/update', [AppController::class, 'AppEditPost'])->name('apps.edit.post')->middleware('auth');
Route::post('/apps/delete', [AppController::class, 'AppDelete'])->name('apps.delete')->middleware('auth');
Route::post('/apps/generate', [AppController::class, 'AppGeneratePost'])->name('apps.generate.post')->middleware('auth');

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});