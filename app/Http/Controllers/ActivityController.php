<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities for a task.
     */
    public function index(Task $task)
    {
        $activities = $task->activities()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // return Inertia::render('', [
        //     'task' => $task,
        //     'activities' => $activities,
        // ]);
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
            'old_value' => 'nullable|string',
            'new_value' => 'nullable|string',
        ]);

        $activity = $task->activities()->create([
            'user_id' => auth()->id(),
            'action' => $validated['action'],
            'description' => $validated['description'] ?? null,
            'old_value' => $validated['old_value'] ?? null,
            'new_value' => $validated['new_value'] ?? null,
        ]);

        return back()->with('success', 'Activity recorded successfully!');
    }

    /**
     * Display all user activities.
     */
    public function userActivities()
    {
        $activities = auth()->user()->activities()
            ->with('task')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Activities/UserActivities', [
            'activities' => $activities,
        ]);
    }

    /**
     * Get activity log for a specific task.
     */
    public function taskLog(Task $task)
    {
        $activities = $task->activities()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'task' => $task,
            'activities' => $activities,
        ]);
    }
}
