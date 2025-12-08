<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{

    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->author_id;
    }

    public function delete(User $user, Comment $comment): bool
    {

        return $user->id === $comment->author_id ||
            $user->id === $comment->task->author_id ||
            $user->id === $comment->task->project->owner_id;
    }
}
