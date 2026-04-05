<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class,'index']);
Route::prefix('/admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class,'index']);
    Route::get('/notifications', [AdminDashboardController::class,'notifications']);
    Route::get('/tasks', [AdminDashboardController::class,'tasks']);
    Route::get('/settings', [AdminDashboardController::class,'settings']);
});
Route::prefix('/employee')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class,'index']);
});

require __DIR__.'/auth.php';
