<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;

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
            'tasks'=> Task::orderBy('id','desc')->get(),
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

        $user = Auth::user();

        // Get employee's assigned tasks
        $assignedTasks = $user->assignedTasks()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
        $stats = [
            'todo'=> $user->assignedTasks()->where('status','todo')->withCount('comments')->get(),
            'progress'=>$user->assignedTasks()->where('status','progress')->withCount('comments')->get(),
            'review'=>$user->assignedTasks()->where('status','review')->withCount('comments')->get(),
            'done'=>$user->assignedTasks()->where('status','done')->withCount('comments')->get(),
        ];
        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'TasksPage',
            'stats'=> $stats
        ]);
    }

    public function showTask(Task $task)
    {
        $user = Auth::user();

        abort_unless(
            $task->created_by === $user->id || $task->assignedEmployees()->where('user_id', $user->id)->exists(),
            403
        );

        $task->load([
            'creator:id,firstname,surname,email',
            'assignedEmployees:id,firstname,surname,email',
            'comments' => function ($query) {
                $query->with('author:id,firstname,surname')->latest();
            },
        ])->loadCount('comments');

        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'TaskDetailsPage',
            'task' => $task,
        ]);
    }

    public function storeComment(Request $request, Task $task)
    {
        $user = Auth::user();

        abort_unless(
            $task->created_by === $user->id || $task->assignedEmployees()->where('user_id', $user->id)->exists(),
            403
        );

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return redirect()->route('employee.tasks.show', $task)->with('success', 'Comment posted.');
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
