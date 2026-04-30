<?php

namespace App\Http\Controllers\Admin;

use App\Events\TaskAssigned;
use App\Events\TaskCreated;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index()
    {
        $tasks = Task::with('creator', 'assignedEmployees')->paginate(15);
        $employees = User::paginate(15);

        return Inertia::render('Admin/Task/Index', [
            'tasks' => $tasks,
            'employees' => $employees,
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $employees = User::all();

        return Inertia::render('Admin/Task/Index', [
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|string|in:todo,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'progress' => 'nullable|integer|min:0|max:100',
            'assignedTo' => 'nullable|array',
            'assignedTo.*' => 'nullable|integer|exists:users,id',

            // 'assignedTo.*' => 'integer|exists:users,id',
        ]);

        $task = Task::create([
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? null,
            'progress' => $validated['progress'] ?? 0,
        ]);

        // Attach employees to the task
        if (!empty($validated['assignedTo'])) {
            $task->assignedEmployees()->sync($validated['assignedTo']);
        }

        $task->load('assignedEmployees');
        $creator = auth()->user();

        // Persist notifications and broadcast to each assigned employee
        foreach ($task->assignedEmployees as $employee) {
            Notification::create([
                'user_id' => $employee->id,
                'type' => 'task_created',
                'title' => 'New Task Assigned',
                'message' => "You have been assigned a new task: \"{$task->title}\"",
                'link' => "/employee/tasks/{$task->id}",
            ]);
        }

        if ($task->assignedEmployees->isNotEmpty()) {
            broadcast(new TaskCreated($task, $creator))->toOthers();
        }

        return redirect()->back()->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load('creator', 'assignedEmployees', 'activities');

        return Inertia::render('Admin/Task/Index', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $task->load('assignedEmployees');
        $employees = User::all();

        return Inertia::render('Admin/Task/Index', [
            'task' => $task,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|string|in:todo,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'progress' => 'nullable|integer|min:0|max:100',
            'assignedTo' => 'nullable|array',
            'assignedTo.*' => 'integer|exists:users,id',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? null,
            'progress' => $validated['progress'] ?? 0,
        ]);

        // Determine newly assigned employees before syncing
        $previousEmployeeIds = $task->assignedEmployees()->pluck('users.id')->toArray();

        // Sync employees
        if (!empty($validated['assignedTo'])) {
            $task->assignedEmployees()->sync($validated['assignedTo']);
        } else {
            $task->assignedEmployees()->detach();
        }

        $newEmployeeIds = array_values(array_diff(
            $validated['assignedTo'] ?? [],
            $previousEmployeeIds
        ));

        // Persist notifications and broadcast to newly assigned employees
        foreach ($newEmployeeIds as $employeeId) {
            Notification::create([
                'user_id' => $employeeId,
                'type' => 'task_assigned',
                'title' => 'Task Assigned to You',
                'message' => "You have been assigned to the task: \"{$task->title}\"",
                'link' => "/employee/tasks/{$task->id}",
            ]);
        }

        if (!empty($newEmployeeIds)) {
            broadcast(new TaskAssigned($task, $newEmployeeIds))->toOthers();
        }

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}
