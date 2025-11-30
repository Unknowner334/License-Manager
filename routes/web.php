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

// * Dashboard
Route::get('/', [DashController::class, 'dashboard'])->middleware('auth', 'session.timeout');
Route::get('/dashboard', [DashController::class, 'dashboard'])->name('dashboard')->middleware('auth', 'session.timeout');

// * Manage Users
Route::get('/admin/users', [DashController::class, 'manageusers'])->name('admin.users')->middleware('auth', 'session.timeout');
Route::get('/admin/users/{id}', [DashController::class, 'manageusersedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit')->middleware('auth', 'session.timeout');
Route::get('/admin/users/generate', [DashController::class, 'manageusersgenerate'])->name('admin.users.generate')->middleware('auth', 'session.timeout');
Route::get('/admin/users/history', [DashController::class, 'manageusershistory'])->name('admin.users.history')->middleware('auth', 'session.timeout');
Route::get('/admin/users/history/{id}', [DashController::class, 'manageusershistoryuser'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.history.user')->middleware('auth', 'session.timeout');
Route::post('/admin/users', [DashController::class, 'manageusersedit_action'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit.post')->middleware('auth', 'session.timeout');
Route::post('/admin/users/generate', [DashController::class, 'manageusersgenerate_action'])->name('admin.users.generate.post')->middleware('auth', 'session.timeout');
Route::post('/admin/users/delete', [DashController::class, 'manageusersdelete'])->name('admin.users.delete')->middleware('auth', 'session.timeout');

// * Manage Referrable Codes
Route::get('/admin/referrables', [DashController::class, 'managereferrable'])->name('admin.referrable')->middleware('auth', 'session.timeout');
Route::get('/admin/referrables/{id}', [DashController::class, 'managereferrableedit'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.referrable.edit')->middleware('auth', 'session.timeout');
Route::get('/admin/referrables/generate', [DashController::class, 'managereferrablegenerate'])->name('admin.referrable.generate')->middleware('auth', 'session.timeout');
Route::post('/admin/referrables/update', [DashController::class, 'managereferrableedit_action'])->name('admin.referrable.edit.post')->middleware('auth', 'session.timeout');
Route::post('/admin/referrables/generate', [DashController::class, 'managereferrablegenerate_action'])->name('admin.referrable.generate.post')->middleware('auth', 'session.timeout');
Route::post('/admin/referrables/delete', [DashController::class, 'managereferrabledelete'])->name('admin.referrable.delete')->middleware('auth', 'session.timeout');

// * API
Route::get('/API/connect', [ApiController::class, 'ApiConnect'])->name('api.connect')->middleware('throttle:10,5');

// * Settings
Route::get('/settings', [SettingController::class, 'settings'])->name('settings')->middleware('auth', 'session.timeout');
Route::post('/settings/username-change', [SettingController::class, 'settingssusername'])->name('settings.username')->middleware('auth', 'session.timeout');
Route::post('/settings/name-change', [SettingController::class, 'settingsname'])->name('settings.name')->middleware('auth', 'session.timeout');
Route::post('/settings/password-change', [SettingController::class, 'settingspassword'])->name('settings.password')->middleware('auth', 'session.timeout');

// * Apps
Route::get('/apps', [AppController::class, 'applist'])->name('apps')->middleware('auth', 'session.timeout');
Route::get('/apps/{id}', [AppController::class, 'appedit'])->where('id', '[0-9a-fA-F-]{36}')->name('apps.edit')->middleware('auth', 'session.timeout');
Route::get('/apps/generate', [AppController::class, 'appgenerate'])->name('apps.generate')->middleware('auth', 'session.timeout');
Route::post('/apps/update', [AppController::class, 'appedit_action'])->name('apps.edit.post')->middleware('auth', 'session.timeout');
Route::post('/apps/delete', [AppController::class, 'appdelete'])->name('apps.delete')->middleware('auth', 'session.timeout');
Route::post('/apps/delete/keys', [AppController::class, 'appdeletekeys'])->name('apps.delete.keys')->middleware('auth', 'session.timeout');
Route::post('/apps/delete/keys/me', [AppController::class, 'appdeletekeysme'])->name('apps.delete.keys.me')->middleware('auth', 'session.timeout');
Route::post('/apps/generate', [AppController::class, 'appgenerate_action'])->name('apps.generate.post')->middleware('auth', 'session.timeout');

// * Keys
Route::get('/keys', [KeyController::class, 'keylist'])->name('keys')->middleware('auth', 'session.timeout');
Route::get('/keys/{id}', [KeyController::class, 'keyedit'])->where('id', '[0-9a-fA-F-]{36}')->name('keys.edit')->middleware('auth', 'session.timeout');
Route::get('/keys/resetApiKey/{id}', [KeyController::class, 'keyresetapi'])->where('id', '[0-9a-fA-F-]{36}')->name('keys.resetApiKey')->middleware('auth', 'session.timeout');
Route::get('/keys/generate', [KeyController::class, 'keygenerate'])->name('keys.generate')->middleware('auth', 'session.timeout');
Route::post('/keys/update', [KeyController::class, 'keyedit_action'])->name('keys.edit.post')->middleware('auth', 'session.timeout');
Route::post('/keys/delete', [KeyController::class, 'keydelete'])->name('keys.delete')->middleware('auth', 'session.timeout');
Route::post('/keys/history/delete', [KeyController::class, 'keyhistorydelete'])->name('keys.history.delete')->middleware('auth', 'session.timeout');
Route::post('/keys/history/delete/all', [KeyController::class, 'keyhistorydeleteall'])->name('keys.history.delete.all')->middleware('auth', 'session.timeout');
Route::post('/keys/generate', [KeyController::class, 'keygenerate_action'])->name('keys.generate.post')->middleware('auth', 'session.timeout');

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});