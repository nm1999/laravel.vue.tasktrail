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

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly User $creator,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        foreach ($this->task->assignedEmployees as $employee) {
            $channels[] = new PrivateChannel("employee.{$employee->id}");
        }

        return $channels;
    }

    /**
     * The event name to broadcast as.
     */
    public function broadcastAs(): string
    {
        return 'task.created';
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
                'description' => $this->task->description,
                'deadline' => $this->task->deadline?->toISOString(),
                'status' => $this->task->status,
                'priority' => $this->task->priority,
                'progress' => $this->task->progress,
            ],
            'creator' => [
                'id' => $this->creator->id,
                'name' => $this->creator->full_name,
            ],
        ];
    }
}
