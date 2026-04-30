<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly User $updatedBy,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * Notifies the task creator (admin) and all other assigned employees.
     *
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Notify the task creator
        $channels[] = new PrivateChannel("employee.{$this->task->created_by}");

        // Notify all assigned employees except the one who made the change
        foreach ($this->task->assignedEmployees as $employee) {
            if ($employee->id !== $this->updatedBy->id) {
                $channels[] = new PrivateChannel("employee.{$employee->id}");
            }
        }

        return $channels;
    }

    /**
     * The event name to broadcast as.
     */
    public function broadcastAs(): string
    {
        return 'task.status_changed';
    }

    /**
     * Data to broadcast with the event.
     */
    public function broadcastWith(): array
    {
        return [
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'status' => $this->newStatus,
                'progress' => $this->task->progress,
            ],
            'updated_by' => [
                'id' => $this->updatedBy->id,
                'name' => $this->updatedBy->full_name,
            ],
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
