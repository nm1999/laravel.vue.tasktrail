<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
    ]);
});
Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/AdminDashboard', [
    ]);
});
Route::get('/employee/dashboard', function () {
    return Inertia::render('Employee/EmployeeDashboard', [
    ]);
});


require __DIR__.'/auth.php';
