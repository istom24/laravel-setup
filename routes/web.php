//use App\Http\Controllers\UserController;
//use App\Http\Controllers\ProjectController;
//use App\Http\Controllers\TaskController;
//use App\Http\Controllers\CommentController;
//use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\TestController;
//
//Route::get('/test-relationships', [TestController::class, 'testRelationships']);
//
//Route::get('/', function () {
//    return view('welcome');
//});
//
//Route::get('/first', [UserController::class, 'first']);
//
//// API Routes
//Route::apiResource('users', UserController::class);
//Route::apiResource('projects', ProjectController::class);
//Route::apiResource('tasks', TaskController::class);
//Route::apiResource('comments', CommentController::class);
//
//// Additional routes
//Route::get('/users/{user}/projects', [UserController::class, 'projects']);
//Route::get('/users/{user}/tasks', [UserController::class, 'tasks']);
//Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks'])->name('projects.tasks');
//Route::get('/tasks/{task}/comments', [TaskController::class, 'taskComments'])->name('tasks.comments');
//
//// Database check route
//Route::get('/check-db', function () {
//    $user = \App\Models\User::first();
//    $project = \App\Models\Project::first();
//    $task = \App\Models\Task::first();
//
//    return [
//        'user' => [
//            'name' => $user->name,
//            'owned_projects' => $user->ownedProjects->count(),
//            'projects' => $user->projects->count(),
//            'created_tasks' => $user->createdTasks->count(),
//        ],
//        'project' => [
//            'name' => $project->name,
//            'owner' => $project->owner->name,
//            'members' => $project->users->count(),
//            'tasks' => $project->tasks->count(),
//        ],
//        'task' => [
//            'title' => $task->title,
//            'project' => $task->project->name,
//            'author' => $task->author->name,
//            'assignee' => $task->assignee->name ?? 'Not assigned',
//            'comments' => $task->comments->count(),
//        ],
//        'counts' => [
//            'users' => \App\Models\User::count(),
//            'projects' => \App\Models\Project::count(),
//            'tasks' => \App\Models\Task::count(),
//            'comments' => \App\Models\Comment::count(),
//            'reports' => \App\Models\Report::count(),
//        ]
//    ];
//});
//
//Route::get('/health', function () {
//    return response()->json([
//        'status' => 'ok',
//        'timestamp' => now(),
//        'database' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'connected' : 'disconnected',
//    ]);
//});
//Route::get('/live', function () {
//    return view('live');
//});
//Route::get('/test', function () {
//    return response()->json([
//        'status' => 'ok',
//        'message' => 'Приложение работает',
//        'time' => now()->toDateTimeString(),
//        'broadcast_driver' => config('broadcasting.default'),
//        'reverb_config' => config('reverb')
//    ]);
//});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::redirect('/', '/test');

Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Додаток працює',
        'time' => now()->toDateTimeString(),
        'broadcast_driver' => config('broadcasting.default'),
        'reverb_config' => config('reverb')
    ]);
});

Route::get('/api/test', [TestController::class, 'index']);

Route::get('/live', function () {
    return view('live');
});


