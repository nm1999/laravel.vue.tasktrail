<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        // Sync employees
        if (!empty($validated['assignedTo'])) {
            $task->assignedEmployees()->sync($validated['assignedTo']);
        } else {
            $task->assignedEmployees()->detach();
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
