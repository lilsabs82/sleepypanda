<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetEmail'])->name('password.email');

// Protected routes (client-side auth check via JWT)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/jurnal', function () {
    return view('jurnal');
})->name('jurnal');

Route::get('/report', function () {
    return view('report');
})->name('report');

Route::get('/database-user', function () {
    return view('database-user');
})->name('database-user');

Route::get('/update-data', function () {
    return view('update-data');
})->name('update-data');

Route::get('/reset-password-admin', function () {
    return view('reset-password-admin');
})->name('reset-password');
