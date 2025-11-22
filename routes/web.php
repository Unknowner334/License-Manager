<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\ApiController;

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

// * Manage Users
Route::get('/admin/users', [DashController::class, 'ManageUsers'])->name('admin.users')->middleware('auth');
Route::get('/admin/users/{id}', [DashController::class, 'ManageUsersEditView'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit')->middleware('auth');
Route::get('/admin/users/generate', [DashController::class, 'ManageUsersGenerateView'])->name('admin.users.generate')->middleware('auth');
Route::get('/admin/users/history', [DashController::class, 'ManageUsersHistoryView'])->name('admin.users.history')->middleware('auth');
Route::get('/admin/users/history/{id}', [DashController::class, 'ManageUsersHistoryUserView'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.history.user')->middleware('auth');
Route::post('/admin/users', [DashController::class, 'ManageUsersEditPost'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.users.edit.post')->middleware('auth');
Route::post('/admin/users/generate', [DashController::class, 'ManageUsersGeneratePost'])->name('admin.users.generate.post')->middleware('auth');
Route::post('/admin/users/delete', [DashController::class, 'ManageUsersDeletePost'])->name('admin.users.delete')->middleware('auth');

// * Manage Referrable Codes
Route::get('/admin/referrables', [DashController::class, 'ManageReferrable'])->name('admin.referrable')->middleware('auth');
Route::get('/admin/referrables/{id}', [DashController::class, 'ManageReferrableEditView'])->where('id', '[0-9a-fA-F-]{36}')->name('admin.referrable.edit')->middleware('auth');
Route::get('/admin/referrables/generate', [DashController::class, 'ManageReferrableGenerateView'])->name('admin.referrable.generate')->middleware('auth');
Route::post('/admin/referrables/update', [DashController::class, 'ManageReferrableEditPost'])->name('admin.referrable.edit.post')->middleware('auth');
Route::post('/admin/referrables/generate', [DashController::class, 'ManageReferrableGeneratePost'])->name('admin.referrable.generate.post')->middleware('auth');
Route::post('/admin/referrables/delete', [DashController::class, 'ManageReferrableDeletePost'])->name('admin.referrable.delete')->middleware('auth');

// * API
Route::get('/API/connect', [ApiController::class, 'ApiConnect'])->name('api.connect');

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
Route::post('/apps/delete/keys', [AppController::class, 'AppDeleteKeys'])->name('apps.delete.keys')->middleware('auth');
Route::post('/apps/delete/keys/me', [AppController::class, 'AppDeleteKeysMe'])->name('apps.delete.keys.me')->middleware('auth');
Route::post('/apps/generate', [AppController::class, 'AppGeneratePost'])->name('apps.generate.post')->middleware('auth');

// * Keys
Route::get('/keys', [KeyController::class, 'KeyListView'])->name('keys')->middleware('auth');
Route::get('/keys/{id}', [KeyController::class, 'KeyEditView'])->where('id', '[0-9a-fA-F-]{36}')->name('keys.edit')->middleware('auth');
Route::get('/keys/history/{id}', [KeyController::class, 'KeyHistoryView'])->where('id', '[0-9a-fA-F-]{36}')->name('keys.history')->middleware('auth');
Route::get('/keys/generate', [KeyController::class, 'KeyGenerateView'])->name('keys.generate')->middleware('auth');
Route::post('/keys/update', [KeyController::class, 'KeyEditPost'])->name('keys.edit.post')->middleware('auth');
Route::post('/keys/delete', [KeyController::class, 'KeyDelete'])->name('keys.delete')->middleware('auth');
Route::post('/keys/history/delete', [KeyController::class, 'KeyHistoryDelete'])->name('keys.history.delete')->middleware('auth');
Route::post('/keys/history/delete/all', [KeyController::class, 'KeyHistoryDeleteAll'])->name('keys.history.delete.all')->middleware('auth');
Route::post('/keys/generate', [KeyController::class, 'KeyGeneratePost'])->name('keys.generate.post')->middleware('auth');

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});