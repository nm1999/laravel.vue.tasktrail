# Firebase Integration Examples

## Example 1: Integrate FCM with TaskAssigned Event

Replace the content of `app/Events/TaskAssigned.php` with:

```php
<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Task $task,
        public User $assignedTo,
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel("tasks.{$this->task->id}");
    }

    public function broadcastAs()
    {
        return 'TaskAssigned';
    }

    public function dispatchPriority()
    {
        // Send FCM push notification when task is assigned
        try {
            $firebaseService = app(FirebaseNotificationService::class);
            $firebaseService->sendTaskAssignmentNotification(
                $this->assignedTo,
                [
                    'task_id' => $this->task->id,
                    'title' => $this->task->title,
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send FCM notification for task assignment', [
                'task_id' => $this->task->id,
                'user_id' => $this->assignedTo->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - let the event continue even if notification fails
        }
    }
}
```

## Example 2: Integrate FCM with TaskCreated Event

Replace the content of `app/Events/TaskCreated.php` with:

```php
<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Task $task,
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel("workspace.{$this->task->workspace_id}");
    }

    public function broadcastAs()
    {
        return 'TaskCreated';
    }

    public function dispatchPriority()
    {
        // Send FCM notification to all assigned users
        try {
            if ($this->task->employees()->exists()) {
                $firebaseService = app(FirebaseNotificationService::class);
                
                // Notify all assigned employees
                foreach ($this->task->employees as $employee) {
                    $firebaseService->sendTaskCreatedNotification(
                        $employee->user,
                        [
                            'task_id' => $this->task->id,
                            'title' => $this->task->title,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send FCM notification for task creation', [
                'task_id' => $this->task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

## Example 3: Integrate FCM with TaskStatusChanged Event

Replace the content of `app/Events/TaskStatusChanged.php` with:

```php
<?php

namespace App\Events;

use App\Models\Task;
use App\Services\FirebaseNotificationService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public string $oldStatus;
    public string $newStatus;

    public function __construct(
        public Task $task,
        string $oldStatus,
        string $newStatus,
    ) {
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("tasks.{$this->task->id}");
    }

    public function broadcastAs()
    {
        return 'TaskStatusChanged';
    }

    public function dispatchPriority()
    {
        // Send FCM notification to all assigned employees
        try {
            if ($this->task->employees()->exists()) {
                $firebaseService = app(FirebaseNotificationService::class);
                
                foreach ($this->task->employees as $employee) {
                    $firebaseService->sendTaskStatusNotification(
                        $employee->user,
                        [
                            'task_id' => $this->task->id,
                            'message' => "Task status changed from {$this->oldStatus} to {$this->newStatus}",
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send FCM notification for task status change', [
                'task_id' => $this->task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

## Example 4: Sending Notifications from Controller

Use this pattern in your controllers:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService,
    ) {}

    /**
     * Create a new task and notify assignees
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'assigned_to' => 'required|array',
        ]);

        // Create task
        $task = Task::create($validated);

        // Notify assigned users
        $assignedUsers = User::whereIn('id', $validated['assigned_to'])->get();
        
        foreach ($assignedUsers as $user) {
            $this->firebaseService->sendTaskAssignmentNotification($user, [
                'task_id' => $task->id,
                'title' => $task->title,
            ]);
        }

        return response()->json($task, 201);
    }

    /**
     * Send batch notification to all managers
     */
    public function notifyManagers(Request $request, Task $task)
    {
        $managers = User::where('role', 'manager')->get();
        $managerIds = $managers->pluck('id')->toArray();

        $this->firebaseService->sendToUsers($managerIds, [
            'title' => 'Task Attention Required',
            'body' => "Task '{$task->title}' needs your attention",
            'click_action' => route('admin.tasks.show', $task->id),
            'data' => [
                'type' => 'task_attention',
                'task_id' => $task->id,
            ],
        ]);

        return response()->json(['message' => 'Notifications sent']);
    }
}
```

## Example 5: Sending Notifications from Jobs

Use this pattern in your queue jobs:

```php
<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskReminderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(
        public Task $task,
    ) {}

    public function handle(FirebaseNotificationService $firebaseService)
    {
        // Send reminder to all assigned employees
        foreach ($this->task->employees as $employee) {
            $firebaseService->sendToUser($employee->user, [
                'title' => 'Task Reminder',
                'body' => "Don't forget: {$this->task->title}",
                'click_action' => route('admin.tasks.show', $this->task->id),
                'data' => [
                    'type' => 'task_reminder',
                    'task_id' => $this->task->id,
                ],
            ]);
        }
    }
}
```

## Example 6: Custom Notifications with Data

Send custom notifications with additional data:

```php
use App\Services\FirebaseNotificationService;

$firebaseService = app(FirebaseNotificationService::class);

$user = auth()->user();

// Send custom notification with rich data
$firebaseService->sendToUser($user, [
    'title' => 'Project Milestone',
    'body' => 'Project "Website Redesign" is 50% complete',
    'click_action' => route('admin.projects.show', 123),
    'data' => [
        'type' => 'project_milestone',
        'project_id' => '123',
        'progress' => '50',
        'project_name' => 'Website Redesign',
        'estimated_days_left' => '14',
    ],
]);
```

## Example 7: Listening to Notifications in Vue Component

Handle notifications in your Vue components:

```vue
<template>
    <div>
        <!-- Component content -->
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

onMounted(() => {
    // Listen for FCM notifications
    window.addEventListener('fcm:notification', (event) => {
        const { notification, data } = event.detail;
        
        console.log('Notification received:', notification);
        console.log('Custom data:', data);

        // Handle different notification types
        switch (data.type) {
            case 'task_assigned':
                // Navigate to task
                router.push({ name: 'tasks.show', params: { id: data.task_id } });
                break;
            
            case 'task_status_changed':
                // Refresh tasks or show notification
                console.log('Task status changed:', data.message);
                break;
            
            case 'project_milestone':
                // Navigate to project
                router.push({ name: 'projects.show', params: { id: data.project_id } });
                break;
            
            default:
                console.log('Unknown notification type:', data.type);
        }
    });
});
</script>
```

## How to Use These Examples

1. **For Events**: Copy the example code and replace your existing event class
2. **For Controllers**: Use the pattern shown to notify users after creating/updating resources
3. **For Jobs**: Create a new job file and follow the pattern for scheduled notifications
4. **For Vue Components**: Add the listener to components that need to react to notifications

## Testing Your Integration

After implementing, test with:

```bash
# In Laravel Tinker
$user = App\Models\User::first();
$task = App\Models\Task::first();

$firebaseService = app(App\Services\FirebaseNotificationService::class);
$firebaseService->sendTaskAssignmentNotification($user, [
    'task_id' => $task->id,
    'title' => $task->title,
]);
```

Check browser console for errors and verify the notification appears!
