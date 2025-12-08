<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }

    public function addMember(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }


    public function createTask(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id ||
            $project->users()->where('user_id', $user->id)->exists();
    }
}
