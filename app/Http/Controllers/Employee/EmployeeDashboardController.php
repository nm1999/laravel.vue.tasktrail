<?php

namespace App\Http\Controllers\Employee;

use App\Events\TaskStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Notification;
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

        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'HomePage',
            'user' => $user,
            'stats' => $stats,
            'recentTasks' => $assignedTasks,
            'unreadCount' => $unreadCount,
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

        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'TasksPage',
            'stats'=> $stats,
            'unreadCount' => $unreadCount,
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
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        return Inertia::render('Employee/EmployeeDashboard', [
            'component' => 'NotificationsPage',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Update a task's status and broadcast the change.
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        abort_unless(
            $task->created_by === $user->id || $task->assignedEmployees()->where('user_id', $user->id)->exists(),
            403
        );

        // Accept both admin-side ('todo','in_progress','completed') and
        // kanban-side ('todo','progress','review','done') status values
        // to maintain compatibility with the existing dual naming convention.
        $validated = $request->validate([
            'status' => 'required|string|in:todo,in_progress,review,completed,progress,done',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $oldStatus = $task->status;
        $task->update([
            'status' => $validated['status'],
            'progress' => $validated['progress'] ?? $task->progress,
        ]);

        $task->load('assignedEmployees');

        // Persist notifications for affected users (creator + other assigned employees)
        $notifyUserIds = collect([$task->created_by]);
        foreach ($task->assignedEmployees as $employee) {
            if ($employee->id !== $user->id) {
                $notifyUserIds->push($employee->id);
            }
        }

        foreach ($notifyUserIds->unique() as $userId) {
            if ($userId !== $user->id) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'task_status_changed',
                    'title' => 'Task Progress Updated',
                    'message' => "{$user->full_name} updated \"{$task->title}\" status to {$validated['status']}",
                    'link' => "/employee/tasks/{$task->id}",
                ]);
            }
        }

        broadcast(new TaskStatusChanged($task, $user, $oldStatus, $validated['status']))->toOthers();

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function index()
    {
        return $this->dashboard();
    }
}
