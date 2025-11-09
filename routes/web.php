<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;

// ? Authentication

// * Login
Route::get('/login', [AuthController::class, 'LoginView'])->name('login');
Route::post('/login', [AuthController::class, 'LoginPost'])->name('login.post')->middleware('throttle:10,5');
Route::post('/logout', [AuthController::class, 'LogoutPost'])->name('logout')->middleware('throttle:10,5');

// * Register
Route::get('/register', function () {return 'Register';})->name('register');

// ? End of Authentication

// ? Dashboard
Route::get('/', [DashController::class, 'Dashboard'])->middleware('auth');
Route::get('/dashboard', [DashController::class, 'Dashboard'])->name('dashboard')->middleware('auth');

// ! Fallback
Route::fallback(function () {return view('errors.fallback');});