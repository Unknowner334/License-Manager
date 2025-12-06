<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;

// * Login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login_action'])->name('login.post')->middleware('throttle:10,5');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// * Register
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register_action'])->name('register.post')->middleware('throttle:10,5');

// * Authenticate
Route::get('/API/connect', [ApiController::class, 'Authenticate'])->name('api.connect')->middleware('throttle:50,5');

Route::middleware('auth', 'session.timeout', 'no.cache')->group(function () {
    // * Dashboard
    Route::get('/', [DashController::class, 'dashboard']);
    Route::get('/dashboard', [DashController::class, 'dashboard'])->name('dashboard');

    // * Dashboard AJAX
    Route::get('/dashboard/licenses/data', [DashController::class, 'licensedata_10'])->name('dashboard.licenses.data');

    // * Manage Users View
    Route::get('/admin/users', [UserController::class, 'manageusers'])->name('admin.users');
    Route::get('/admin/users/{id?}', [UserController::class, 'manageusersedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit');
    Route::get('/admin/users/generate', [UserController::class, 'manageusersgenerate'])->name('admin.users.generate');
    Route::get('/admin/users/history/{id?}', [UserController::class, 'manageusershistoryuser'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.history.user');
    Route::get('/admin/users/wallet/{id?}', [UserController::class, 'manageuserssaldoedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.wallet');

    // * Manage Users AJAX
    Route::get('/admin/users/data', [UserController::class, 'manageusersdata'])->name('admin.users.data');
    Route::get('/admin/users/history/data/{id?}', [UserController::class, 'manageusershistorydata'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.history.data');

    // * Manage Users Manage
    Route::post('/admin/users', [UserController::class, 'manageusersedit_action'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit.post');
    Route::post('/admin/users/generate', [UserController::class, 'manageusersgenerate_action'])->name('admin.users.generate.post');
    Route::post('/admin/users/delete', [UserController::class, 'manageusersdelete'])->name('admin.users.delete');
    Route::post('/admin/users/wallet', [UserController::class, 'manageuserssaldoedit_action'])->name('admin.users.wallet.post');

    // * Manage Referrables View
    Route::get('/admin/referrables', [DashController::class, 'managereferrable'])->name('admin.referrable');
    Route::get('/admin/referrables/{id?}', [DashController::class, 'managereferrableedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.referrable.edit');
    Route::get('/admin/referrables/generate', [DashController::class, 'managereferrablegenerate'])->name('admin.referrable.generate');
    
    // * Manage Referrable AJAX
    Route::get('/admin/referrables/data', [DashController::class, 'managereferrabledata'])->name('admin.referrable.data');

    // * Manage Referrable Manage
    Route::post('/admin/referrables/update', [DashController::class, 'managereferrableedit_action'])->name('admin.referrable.edit.post');
    Route::post('/admin/referrables/generate', [DashController::class, 'managereferrablegenerate_action'])->name('admin.referrable.generate.post');
    Route::post('/admin/referrables/delete', [DashController::class, 'managereferrabledelete'])->name('admin.referrable.delete');

    // * Settings
    Route::get('/settings', [SettingController::class, 'settings'])->name('settings');
    Route::post('/settings/username-change', [SettingController::class, 'settingsusername'])->name('settings.username');
    Route::post('/settings/name-change', [SettingController::class, 'settingsname'])->name('settings.name');
    Route::post('/settings/password-change', [SettingController::class, 'settingspassword'])->name('settings.password');

    // * Apps View
    Route::get('/apps', [AppController::class, 'applist'])->name('apps');
    Route::get('/apps/{id?}', [AppController::class, 'appedit'])->where('id', '[0-9a-fA-F-]{36}')->name('apps.edit');
    Route::get('/apps/generate', [AppController::class, 'appgenerate'])->name('apps.generate');

    // * Apps AJAX
    Route::get('/apps/data', [AppController::class, 'appdata'])->name('apps.data');

    // * Apps Manage
    Route::post('/apps/update', [AppController::class, 'appedit_action'])->name('apps.edit.post');
    Route::post('/apps/delete', [AppController::class, 'appdelete'])->name('apps.delete');
    Route::post('/apps/delete/licenses', [AppController::class, 'appdeletelicenses'])->name('apps.delete.licenses');
    Route::post('/apps/delete/licenses/me', [AppController::class, 'appdeletelicensesme'])->name('apps.delete.licenses.me');
    Route::post('/apps/generate', [AppController::class, 'appgenerate_action'])->name('apps.generate.post');

    // * Licenses View
    Route::get('/licenses', [LicenseController::class, 'licenselist'])->name('licenses');
    Route::get('/licenses/{id?}', [LicenseController::class, 'licenseedit'])->where('id', '[0-9a-fA-F-]{36}')->name('licenses.edit');
    Route::get('/licenses/generate', [LicenseController::class, 'licensegenerate'])->name('licenses.generate');

    // * Licenses AJAX
    Route::get('/licenses/data', [LicenseController::class, 'licensedata'])->name('licenses.data');

    // * Licenses Manage
    Route::get('/licenses/resetApiKey/{id?}', [LicenseController::class, 'licenseresetapi'])->where('id', '[0-9a-fA-F-]{36}')->name('licenses.resetApiKey');
    Route::post('/licenses/update', [LicenseController::class, 'licenseedit_action'])->name('licenses.edit.post');
    Route::post('/licenses/delete', [LicenseController::class, 'licensedelete'])->name('licenses.delete');
    Route::post('/licenses/generate', [LicenseController::class, 'licensegenerate_action'])->name('licenses.generate.post');
});

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});