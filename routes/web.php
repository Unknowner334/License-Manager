<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\ApiController;

// * Login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login_action'])->name('login.post')->middleware('throttle:10,5');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('throttle:10,5');

// * Register
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register_action'])->name('register.post')->middleware('throttle:10,5');

Route::get('/API/connect', [ApiController::class, 'ApiConnect'])->name('api.connect')->middleware('throttle:10,5');

Route::middleware('auth', 'session.timeout', 'no.cache')->group(function () {
    // * Dashboard
    Route::get('/', [DashController::class, 'dashboard']);
    Route::get('/dashboard', [DashController::class, 'dashboard'])->name('dashboard');

    // * Manage Users
    Route::get('/admin/users', [DashController::class, 'manageusers'])->name('admin.users');
    Route::get('/admin/users/{id}', [DashController::class, 'manageusersedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit');
    Route::get('/admin/users/wallet/{id}', [DashController::class, 'manageuserssaldoedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.wallet');
    Route::get('/admin/users/generate', [DashController::class, 'manageusersgenerate'])->name('admin.users.generate');
    Route::get('/admin/users/history', [DashController::class, 'manageusershistory'])->name('admin.users.history');
    Route::get('/admin/users/history/{id}', [DashController::class, 'manageusershistoryuser'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.history.user');
    Route::post('/admin/users', [DashController::class, 'manageusersedit_action'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit.post');
    Route::post('/admin/users/generate', [DashController::class, 'manageusersgenerate_action'])->name('admin.users.generate.post');
    Route::post('/admin/users/delete', [DashController::class, 'manageusersdelete'])->name('admin.users.delete');
    Route::post('/admin/users/wallet', [DashController::class, 'manageuserssaldoedit_action'])->name('admin.users.wallet.post');

    // * Manage Referrables
    Route::get('/admin/referrables', [DashController::class, 'managereferrable'])->name('admin.referrable');
    Route::get('/admin/referrables/{id}', [DashController::class, 'managereferrableedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.referrable.edit');
    Route::get('/admin/referrables/generate', [DashController::class, 'managereferrablegenerate'])->name('admin.referrable.generate');
    Route::post('/admin/referrables/update', [DashController::class, 'managereferrableedit_action'])->name('admin.referrable.edit.post');
    Route::post('/admin/referrables/generate', [DashController::class, 'managereferrablegenerate_action'])->name('admin.referrable.generate.post');
    Route::post('/admin/referrables/delete', [DashController::class, 'managereferrabledelete'])->name('admin.referrable.delete');

    // * Settings
    Route::get('/settings', [SettingController::class, 'settings'])->name('settings');
    Route::post('/settings/username-change', [SettingController::class, 'settingssusername'])->name('settings.username');
    Route::post('/settings/name-change', [SettingController::class, 'settingsname'])->name('settings.name');
    Route::post('/settings/password-change', [SettingController::class, 'settingspassword'])->name('settings.password');

    // * Apps
    Route::get('/apps', [AppController::class, 'applist'])->name('apps');
    Route::get('/apps/{id}', [AppController::class, 'appedit'])->where('id', '[0-9a-fA-F-]{36}')->name('apps.edit');
    Route::get('/apps/generate', [AppController::class, 'appgenerate'])->name('apps.generate');
    Route::post('/apps/update', [AppController::class, 'appedit_action'])->name('apps.edit.post');
    Route::post('/apps/delete', [AppController::class, 'appdelete'])->name('apps.delete');
    Route::post('/apps/delete/keys', [AppController::class, 'appdeletekeys'])->name('apps.delete.keys');
    Route::post('/apps/delete/keys/me', [AppController::class, 'appdeletekeysme'])->name('apps.delete.keys.me');
    Route::post('/apps/generate', [AppController::class, 'appgenerate_action'])->name('apps.generate.post');

    // * Keys
    Route::get('/keys', [KeyController::class, 'keylist'])->name('keys');
    Route::get('/keys/{id}', [KeyController::class, 'keyedit'])->where('id', '[0-9a-fA-F-]{36}')->name('keys.edit');
    Route::get('/keys/resetApiKey/{id}', [KeyController::class, 'keyresetapi'])->where('id', '[0-9a-fA-F-]{36}')->name('keys.resetApiKey');
    Route::get('/keys/generate', [KeyController::class, 'keygenerate'])->name('keys.generate');
    Route::post('/keys/update', [KeyController::class, 'keyedit_action'])->name('keys.edit.post');
    Route::post('/keys/delete', [KeyController::class, 'keydelete'])->name('keys.delete');
    Route::post('/keys/history/delete', [KeyController::class, 'keyhistorydelete'])->name('keys.history.delete');
    Route::post('/keys/history/delete/all', [KeyController::class, 'keyhistorydeleteall'])->name('keys.history.delete.all');
    Route::post('/keys/generate', [KeyController::class, 'keygenerate_action'])->name('keys.generate.post');
});

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});