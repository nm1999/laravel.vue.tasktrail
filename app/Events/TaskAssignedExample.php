<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Example: Updated TaskAssigned Event with Firebase Notifications
 * 
 * To use this, replace the content of your app/Events/TaskAssigned.php
 * with this implementation.
 */
class TaskAssignedExample implements ShouldBroadcast
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

    /**
     * Send FCM notification when task is assigned.
     * This method is called when the event is dispatched.
     */
    public function dispatchPriority()
    {
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
        }
    }

    public function broadcastAs()
    {
        return 'TaskAssigned';
    }
}
