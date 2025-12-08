<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/send-test-event', function () {
    broadcast(new \App\Events\TestEvent('Тестовое сообщение от сервера'));
    return response()->json(['message' => 'Test event sent']);
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::apiResource('projects', ProjectController::class);

    Route::prefix('projects/{project}')->middleware('project.access')->group(function () {
        Route::get('/tasks', [TaskController::class, 'projectTasks']);
        Route::post('/tasks', [TaskController::class, 'storeInProject']);
    });

    Route::apiResource('tasks', TaskController::class);

    Route::apiResource('comments', CommentController::class)->except(['index', 'show']);
    Route::get('/tasks/{task}/comments', [CommentController::class, 'taskComments']);
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);

    Route::apiResource('users', UserController::class)->except(['store']);
    Route::get('/users/{user}/projects', [UserController::class, 'projects']);
    Route::get('/users/{user}/tasks', [UserController::class, 'tasks']);
});
