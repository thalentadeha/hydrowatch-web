<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\UserManagerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\User\UserContainerController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserSettingController;
use DebugBar\DebugBar;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login_GET');
Route::post('/login', [AuthController::class, 'login'])->name('login_POST');

Route::middleware('login')->group(function () {
    //ADMIN
    Route::middleware('admin')->group(function () {
        Route::get('admin/toDashboard', [AdminDashboardController::class, 'passToken'])->name('admin-dashboard-pass-token');
        Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin-dashboard');

        //user management
        // Route::get('admin/register-user', [UserManagerController::class, 'showRegister'])->name('register_GET');
        Route::post('admin/register-user', [UserManagerController::class, 'register'])->name('register_POST');
        Route::post('admin/delete-user', [UserManagerController::class, 'deleteUser'])->name('deleteUser_POST');

        //setting
        Route::get('admin/toSetting', [AdminSettingController::class, 'passToken'])->name('admin-setting-pass-token');
        Route::get('admin/setting', [AdminSettingController::class, 'showSetting'])->name('admin-setting');
        Route::post('admin/changePassword', [AdminSettingController::class, 'changePassword'])->name('resetPassword_POST');
    });

    //USER
    Route::middleware('user')->group(function () {
        //dashboard
        Route::get('/toDashboard', [UserDashboardController::class, 'passToken'])->name('user-dashboard-pass-token');
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user-dashboard');

        //container
        Route::get('/toContainer', [UserContainerController::class, 'passToken'])->name('user-container-pass-token');
        Route::get('/container', [UserContainerController::class, 'index'])->name('user-container');

        //setting
        Route::get('/toSetting', [UserSettingController::class, 'passToken'])->name('user-setting-pass-token');
        Route::get('/setting', [UserSettingController::class, 'index'])->name('user-setting');

    });

    Route::get('migrate', [MigrationController::class, 'migrate_realtime_firestore']);

    Route::post('logout}', [AuthController::class, 'logout'])->name('logout_POST');
});

// Route::get('/regtemp', function() {
//     return view('admin.register_temp');
// });
