<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\UserManagerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\User\UserContainerController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\UserSettingController;
use DebugBar\DebugBar;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login_GET');
Route::post('/login', [AuthController::class, 'login'])->name('login_POST');

Route::middleware('login')->group(function () {
    //ADMIN
    Route::middleware('admin')->group(function () {
        Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin-dashboard');

        //user management
        // Route::get('admin/register-user', [UserManagerController::class, 'showRegister'])->name('register_GET');
        Route::post('admin/register-user', [UserManagerController::class, 'register'])->name('register_POST');
        Route::post('admin/delete-user', [UserManagerController::class, 'deleteUser'])->name('deleteUser_POST');

        //setting
        Route::get('admin/setting', [AdminSettingController::class, 'showSetting'])->name('admin-setting');
    });

    //USER
    Route::middleware('user')->group(function () {
        //dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user-dashboard');
        Route::post('/update-month', [UserDashboardController::class, 'updateMonth']);

        //container
        Route::get('/container', [UserContainerController::class, 'index'])->name('user-container');
        Route::post('addContainer', [UserContainerController::class, 'addContainer'])->name('addContainer_POST');
        Route::post('deleteContainer', [UserContainerController::class, 'deleteContainer'])->name('deleteContainer_POST');

        //setting
        Route::get('/setting', [UserSettingController::class, 'index'])->name('user-setting');
        Route::post('changeNickname', [UserSettingController::class, 'changeNickname'])->name('changeNickname_POST');
        Route::post('setMaxDrink', [UserSettingController::class, 'setMaxDrink'])->name('setMaxDrink_POST');
        Route::post('setTargetDrink', [UserSettingController::class, 'setTargetDrink'])->name('setTargetDrink_POST');
        Route::post('saveSchedule', [UserSettingController::class, 'saveSchedule'])->name('saveSchedule_POST');
        Route::post('updateNotificationStatus', [UserSettingController::class, 'updateNotificationStatus'])->name('updateNotificationStatus_POST');
    });

    Route::post('changePassword', [UserManagerController::class, 'changePassword'])->name('resetPassword_POST');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout_POST');
});
