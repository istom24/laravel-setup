<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('project.access')->only(['show', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $projects = $user->projects()
            ->with(['owner', 'users', 'tasks'])
            ->paginate(10);

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['owner_id'] = $request->user()->id;
        $project = Project::create($validated);
        $project->users()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json($project->load(['owner', 'users']), 201);
    }

    public function show(Project $project)
    {
        return $project->load(['owner', 'users', 'tasks.comments.author']);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        return $project->load(['owner', 'users']);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }

    public function addMember(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,member,viewer',
        ]);

        $project->users()->syncWithoutDetaching([
            $request->user_id => ['role' => $request->role]
        ]);

        return response()->json(['message' => 'Користувача додано успішно']);
    }
}
