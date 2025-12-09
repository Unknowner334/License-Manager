<?php

use Illuminate\Support\Facades\Route;
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
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'register_action'])->name('register.post')->middleware('throttle:10,5');
});

Route::prefix('API')->name('api.')->group(function () {
    Route::get('/connect', [ApiController::class, 'Authenticate'])->name('connect')->middleware('throttle:50,5');
});

Route::middleware('auth', 'session.timeout', 'no.cache')->group(function () {
    Route::get('/', [DashController::class, 'dashboard']);
    Route::get('/dashboard', [DashController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [DashController::class, 'licensedata_10'])->name('dashboard.data');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'manageusers'])->name('index');
            Route::get('/{id?}', [UserController::class, 'manageusersedit'])->where('id', '[0-9a-fA-F-]{36}')->name('edit');
            Route::get('/generate', [UserController::class, 'manageusersgenerate'])->name('generate');
            Route::get('/history/{id?}', [UserController::class, 'manageusershistoryuser'])->where('id', '[0-9a-fA-F-]{36}')->name('history.user');
            Route::get('/wallet/{id?}', [UserController::class, 'manageuserssaldoedit'])->where('id', '[0-9a-fA-F-]{36}')->name('wallet');
            Route::get('/data', [UserController::class, 'manageusersdata'])->name('data');
            Route::get('/history/data/{id?}', [UserController::class, 'manageusershistorydata'])->where('id', '[0-9a-fA-F-]{36}')->name('history.data');

            Route::post('/', [UserController::class, 'manageusersedit_action'])->where('id', '[0-9a-fA-F-]{36}')->name('edit.post');
            Route::post('/generate', [UserController::class, 'manageusersgenerate_action'])->name('generate.post');
            Route::post('/delete', [UserController::class, 'manageusersdelete'])->name('delete');
            Route::post('/wallet', [UserController::class, 'manageuserssaldoedit_action'])->name('wallet.post');
        });

        Route::prefix('referrables')->name('referrable.')->group(function () {
            Route::get('/', [DashController::class, 'managereferrable'])->name('index');
            Route::get('/{id?}', [DashController::class, 'managereferrableedit'])->where('id', '[0-9a-fA-F-]{36}')->name('edit');
            Route::get('/generate', [DashController::class, 'managereferrablegenerate'])->name('generate');
            Route::get('/data', [DashController::class, 'managereferrabledata'])->name('data');

            Route::post('/update', [DashController::class, 'managereferrableedit_action'])->name('edit.post');
            Route::post('/generate', [DashController::class, 'managereferrablegenerate_action'])->name('generate.post');
            Route::post('/delete', [DashController::class, 'managereferrabledelete'])->name('delete');
        });
    });

    Route::get('/settings', [SettingController::class, 'settings'])->name('settings');
    Route::post('/settings/username-change', [SettingController::class, 'settingsusername'])->name('settings.username');
    Route::post('/settings/name-change', [SettingController::class, 'settingsname'])->name('settings.name');
    Route::post('/settings/password-change', [SettingController::class, 'settingspassword'])->name('settings.password');

    Route::get('/settings/webui', [WebuiController::class, 'webui_settings'])->name('webui.settings');

    Route::get('/apps', [AppController::class, 'applist'])->name('apps');
    Route::get('/apps/{id?}', [AppController::class, 'appedit'])->where('id', '[0-9a-fA-F-]{36}')->name('apps.edit');
    Route::get('/apps/generate', [AppController::class, 'appgenerate'])->name('apps.generate');

    Route::get('/ajax/apps/data', [AppController::class, 'appdata'])->name('apps.data');

    Route::post('/apps/update', [AppController::class, 'appedit_action'])->name('apps.edit.post');
    Route::post('/apps/delete', [AppController::class, 'appdelete'])->name('apps.delete');
    Route::post('/apps/delete/licenses', [AppController::class, 'appdeletelicenses'])->name('apps.delete.licenses');
    Route::post('/apps/delete/licenses/me', [AppController::class, 'appdeletelicensesme'])->name('apps.delete.licenses.me');
    Route::post('/apps/generate', [AppController::class, 'appgenerate_action'])->name('apps.generate.post');

    Route::get('/licenses', [LicenseController::class, 'licenselist'])->name('licenses');
    Route::get('/licenses/{id?}', [LicenseController::class, 'licenseedit'])->where('id', '[0-9a-fA-F-]{36}')->name('licenses.edit');
    Route::get('/licenses/generate', [LicenseController::class, 'licensegenerate'])->name('licenses.generate');

    Route::get('/ajax/licenses/data', [LicenseController::class, 'licensedata'])->name('licenses.data');

    Route::get('/licenses/resetApiKey/{id?}', [LicenseController::class, 'licenseresetapi'])->where('id', '[0-9a-fA-F-]{36}')->name('licenses.resetApiKey');
    Route::post('/licenses/update', [LicenseController::class, 'licenseedit_action'])->name('licenses.edit.post');
    Route::post('/licenses/delete', [LicenseController::class, 'licensedelete'])->name('licenses.delete');
    Route::post('/licenses/generate', [LicenseController::class, 'licensegenerate_action'])->name('licenses.generate.post');
});

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});