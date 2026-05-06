<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee =  \App\Models\User::all();

        // Get dashboard statistics
        $stats = [
            'total_tasks' => \App\Models\Task::count(),
            'active_tasks' => \App\Models\Task::where('status', 'in_progress')->count(),
            'total_employees' => \App\Models\User::whereHas('roles', function($query) {
                $query->where('role', 'employee');
            })->count(),
            'completed_tasks' => \App\Models\Task::where('status', 'completed')->count(),
        ];

        return Inertia::render('Admin/AdminDashboard', [
            'user' => $user,
            'stats' => $stats,
            'employee'=> $employee
        ]);
    }

    public function notifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function tasks()
    {
        return redirect()->route('admin.tasks.index');
    }

    public function settings()
    {
        return redirect()->route('admin.settings.index');
    }

    public function employees()
    {
        return redirect()->route('admin.employees.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
