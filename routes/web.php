<?php

use App\Http\Controllers\AuthController;
use DebugBar\DebugBar;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login_GET');
Route::post('/login', [AuthController::class, 'login'])->name('login_POST');
Route::post('logout', [AuthController::class, 'logout'])->name('logout_POST');

Route::middleware(['login', 'admin'])->group(function () {
    Route::get('admin/dashboard', function(){
        return view('admin.dashboard');
    })->name('admin-dashboard');

    //user management
    Route::get('admin/register-user', [AuthController::class, 'showRegister'])->name('register_GET');
    Route::post('admin/register-user', [AuthController::class, 'register'])->name('register_POST');
});
