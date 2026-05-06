<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = User::all();
        $recentTasks = Task::with('assignedEmployees:id,firstname,surname')
            ->latest()
            ->take(8)
            ->get(['id', 'title', 'status', 'deadline']);

        // Get dashboard statistics
        $stats = [
            'total_tasks' => Task::count(),
            'active_tasks' => Task::where('status', 'in_progress')->count(),
            'total_employees' => User::whereHas('roles', function($query) {
                $query->where('role', 'employee');
            })->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
        ];

        return Inertia::render('Admin/AdminDashboard', [
            'user' => $user,
            'stats' => $stats,
            'employee'=> $employee,
            'recentTasks' => $recentTasks,
        ]);
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications,
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
