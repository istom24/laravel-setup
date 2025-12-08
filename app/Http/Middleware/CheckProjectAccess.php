<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->route('id') ??
            $request->route('project') ??
            $request->route('project_id');

        if (!$projectId) {
            return response()->json(['message' => 'Проект не вказано'], 400);
        }

        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Проект не знайдено'], 404);
        }

        $user = $request->user();

        $isOwner = $project->owner_id === $user->id;
        $isMember = $project->users()->where('user_id', $user->id)->exists();

        if (!$isOwner && !$isMember) {
            return response()->json([
                'message' => 'Доступ заборонено. Ви не є учасником цього проекту'
            ], 403);
        }

        $request->attributes->set('project', $project);

        return $next($request);
    }
}
