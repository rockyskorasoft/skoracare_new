<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DoctorController;
use App\Http\Controllers\Web\PackageController;
use App\Http\Controllers\Web\ClinicController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginView'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('post-login');

    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    /* Public self-registration (Doctor / Patient signup) */
    Route::get('/signup',  [\App\Http\Controllers\Web\SignupController::class, 'showForm'])->name('signup');
    Route::post('/signup', [\App\Http\Controllers\Web\SignupController::class, 'register'])->name('signup.submit');
});

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes for all authenticated users (Doctors, Admins, etc.)
Route::middleware(['auth'])->as('admin.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::get('/profile', [AuthController::class, 'editProfile'])->name('edit-user-profile');
    Route::post('/profile/update/{id}', [AuthController::class, 'updateProfile'])->name('update.user-profile');
    Route::get('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password/update', [AuthController::class, 'updatePassword'])->name('update-password');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('staff', \App\Http\Controllers\Web\StaffController::class);
    Route::resource('clinics',  ClinicController::class);
    Route::post('/switch-clinic', [ClinicController::class, 'switchClinic'])->name('switch-clinic');
    Route::get('/packages/pricing', [PackageController::class, 'pricing'])->name('packages.pricing');
    Route::resource('packages', PackageController::class);
    Route::resource('activity-log', ActivityLogController::class);
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

