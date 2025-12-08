<?php


namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{

    public function view(User $user, Task $task): bool
    {

        return $user->id === $task->author_id ||
            $user->id === $task->assignee_id ||
            $user->id === $task->project->owner_id ||
            $task->project->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->author_id ||
            $user->id === $task->project->owner_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->author_id ||
            $user->id === $task->project->owner_id;
    }
}
