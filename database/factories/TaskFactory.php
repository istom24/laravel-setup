<?php

namespace Database\Seeders;

use App\Data\TaskFlowData;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskFlowSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect();

        foreach (TaskFlowData::users() as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            $users->push($user);
        }


        $projects = collect();

        foreach (TaskFlowData::projects() as $index => $projectData) {
            $project = Project::create([
                'owner_id' => $users[$index]->id,
                'name' => $projectData['name'],
                'description' => $projectData['description'],
            ]);
            $projects->push($project);
        }


        foreach ($projects as $project) {
            $project->users()->attach($project->owner_id, ['role' => 'owner']);

            $randomUsers = $users->where('id', '!=', $project->owner_id)
                ->random(rand(3, 5));

            foreach ($randomUsers as $user) {
                $project->users()->attach($user->id, [
                    'role' => TaskFlowData::roles()[array_rand(TaskFlowData::roles())]
                ]);
            }

            for ($i = 0; $i < rand(5, 8); $i++) {
                $task = Task::create([
                    'project_id' => $project->id,
                    'author_id' => $project->owner_id,
                    'assignee_id' => $users->random()->id,
                    'title' => TaskFlowData::taskTitles()[array_rand(TaskFlowData::taskTitles())],
                    'description' => 'Детальний опис задачі. Необхідно виконати до вказаного терміну.',
                    'status' => TaskFlowData::statuses()[array_rand(TaskFlowData::statuses())],
                    'priority' => TaskFlowData::priorities()[array_rand(TaskFlowData::priorities())],
                    'due_date' => now()->addDays(rand(1, 30)),
                ]);

                for ($j = 0; $j < rand(2, 4); $j++) {
                    Comment::create([
                        'task_id' => $task->id,
                        'author_id' => $users->random()->id,
                        'body' => TaskFlowData::commentMessages()[array_rand(TaskFlowData::commentMessages())],
                    ]);
                }
            }
        }

        Report::factory(5)->create();
    }
}
