<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $userId;

    public function __construct(Task $task, $userId = null)
    {
        $this->task = $task;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        // Приватний канал для конкретного проекту
        return new PresenceChannel('project.' . $this->task->project_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->task->id,
            'title' => $this->task->title,
            'status' => $this->task->status,
            'priority' => $this->task->priority,
            'project_id' => $this->task->project_id,
            'author_id' => $this->task->author_id,
            'assignee_id' => $this->task->assignee_id,
            'updated_at' => $this->task->updated_at->toDateTimeString(),
            'user_id' => $this->userId,
        ];
    }

    public function broadcastAs()
    {
        return 'task.updated';
    }
}
