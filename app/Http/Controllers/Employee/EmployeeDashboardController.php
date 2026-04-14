<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmployeeDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get employee's assigned tasks
        $assignedTasks = $user->assignedTasks()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get task statistics for the employee
        $stats = [
            'assigned_tasks' => $assignedTasks->count(),
            'completed_tasks' => $user->assignedTasks()->where('status', 'completed')->count(),
            'in_progress_tasks' => $user->assignedTasks()->where('status', 'in_progress')->count(),
            'pending_tasks' => $user->assignedTasks()->where('status', 'todo')->count(),
        ];

        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'HomePage',
            'user' => $user,
            'stats' => $stats,
            'recentTasks' => $assignedTasks,
        ]);
    }

    public function tasks()
    {
        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'TasksPage',
        ]);
    }

    public function notifications()
    {
        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'NotificationsPage',
        ]);
    }

    public function index()
    {
        return $this->dashboard();
    }
}
