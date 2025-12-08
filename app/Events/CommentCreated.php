<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $userId;

    public function __construct(Comment $comment, $userId = null)
    {
        $this->comment = $comment;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        // Приватний канал для проекту
        return new PresenceChannel('project.' . $this->comment->task->project_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->comment->id,
            'body' => $this->comment->body,
            'task_id' => $this->comment->task_id,
            'author_id' => $this->comment->author_id,
            'author_name' => $this->comment->author->name,
            'created_at' => $this->comment->created_at->toDateTimeString(),
            'user_id' => $this->userId,
        ];
    }

    public function broadcastAs()
    {
        return 'comment.created';
    }
}
