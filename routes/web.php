<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminProfilController;

Route::get('/', function () {
    return view('welcome');
});

// /* Admin */
// Route::get('/admin/home', [AdminHomeController::class, 'index'])->name('admin_home')->middleware('admin:admin');;
// Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin_login');
// Route::post('/admin/login-submit', [AdminLoginController::class, 'login_submit'])->name('admin_login_submit');
// Route::get('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin_logout');
// Route::get('/admin/forget-password', [AdminLoginController::class, 'forget_password'])->name('admin_forget_password');
// Route::post('/admin/forget-password-submit', [AdminLoginController::class, 'forget_password_submit'])->name('admin_forget_password_submit');
// Route::get('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password'])->name('admin_reset_password');
// Route::post('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password_submit'])->name('admin_reset_password_submit');

/* ---------------------- Admin ---------------------- */

// Dashboard
Route::get('/admin/home', [AdminHomeController::class, 'index'])
    ->name('admin_home')
    ->middleware('admin:admin');

// Login
Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin_login');
Route::post('/admin/login-submit', [AdminLoginController::class, 'login_submit'])->name('admin_login_submit');

// Logout
Route::get('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin_logout');

// Forget password
Route::get('/admin/forget-password', [AdminLoginController::class, 'forget_password'])->name('admin_forget_password');
Route::post('/admin/forget-password-submit', [AdminLoginController::class, 'forget_password_submit'])->name('admin_forget_password_submit');

// Reset pwd
Route::get('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password'])->name('admin_reset_password');
Route::post('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password_submit'])->name('admin_reset_password_submit');

// Edit profile (GET + POST) ✅ middleware admin appliqué sur les deux
Route::middleware('admin:admin')->group(function () {
    Route::get('/admin/edit-profile', [AdminProfilController::class, 'index'])->name('admin_profile');
    Route::post('/admin/edit-profile-submit', [AdminProfilController::class, 'profile_submit'])->name('admin_profile_submit');
});