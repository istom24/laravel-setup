<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project; // Добавьте эту строку
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = Task::with(['project', 'author', 'assignee', 'comments'])
            ->orderBy('created_at', 'desc');

        // Фільтрація за параметрами
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $validated['author_id'] = $request->user()->id;

        $task = Task::create($validated);
        return response()->json($task->load(['project', 'author', 'assignee']), 201);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $validated['author_id'] = $request->user()->id;

        $task = Task::create($validated);

        // Отправляем событие WebSocket
        broadcast(new \App\Events\TaskCreated($task, $request->user()->id));

        return response()->json($task->load(['project', 'author', 'assignee']), 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return $task->load(['project', 'author', 'assignee', 'comments.author']);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:todo,in_progress,review,done',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);
        return $task->load(['project', 'author', 'assignee']);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->noContent();
    }

    public function projectTasks(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = Task::where('project_id', $project->id)
            ->with(['author', 'assignee', 'comments'])
            ->paginate(10);
        return response()->json($tasks);
    }

    public function storeInProject(Request $request, Project $project)
    {
        $this->authorize('createTask', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $validated['project_id'] = $project->id;
        $validated['author_id'] = $request->user()->id;

        $task = Task::create($validated);
        return response()->json($task->load(['project', 'author', 'assignee']), 201);
    }
}
