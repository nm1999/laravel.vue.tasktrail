<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\ActivityController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Routes
Route::get('/', [HomeController::class,'index'])->middleware('guest')->name('home');

Route::prefix('/api/v1')->group(function () {
   
});


// Admin Routes (Protected)
Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');
    Route::get('/notifications', [AdminDashboardController::class,'notifications'])->name('notifications');
    Route::resource('/tasks', TaskController::class)->names('tasks');
    Route::resource('/employees', EmployeeController::class)->names('employees');
    Route::get('/settings', [SettingsController::class,'index'])->name('settings.index');
    Route::patch('/settings/appearance', [SettingsController::class,'updateAppearance'])->name('settings.appearance');
    Route::patch('/settings/profile', [SettingsController::class,'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [SettingsController::class,'updatePassword'])->name('settings.password');
    Route::patch('/settings/notifications', [SettingsController::class,'updateNotifications'])->name('settings.notifications');
    Route::patch('/settings/security', [SettingsController::class,'updateSecurity'])->name('settings.security');
    Route::post('/logout', [AdminDashboardController::class,'logout'])->name('logout');
});

// Employee Routes (Protected)
Route::middleware(['auth'])->prefix('/employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class,'index'])->name('dashboard');
    Route::get('/tasks', [EmployeeDashboardController::class,'tasks'])->name('tasks');
    Route::get('/notifications', [EmployeeDashboardController::class,'notifications'])->name('notifications');
});

// Shared Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::resource('notifications', NotificationController::class)->only(['index', 'destroy']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.unread');
    Route::patch('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    Route::resource('workspaces', WorkspaceController::class);
    Route::resource('activities', ActivityController::class)->only(['index', 'store']);
    Route::get('user/activities', [ActivityController::class, 'userActivities'])->name('user.activities');
    Route::get('tasks/{task}/activities', [ActivityController::class, 'taskLog'])->name('tasks.activities');
});

require __DIR__.'/auth.php';
