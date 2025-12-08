<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        return Comment::with(['task', 'author'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'author_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $comment = Comment::create($validated);
        return response()->json($comment->load(['task', 'author']), 201);
    }

    public function show(Comment $comment)
    {
        return $comment->load(['task', 'author']);
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $comment->update($validated);
        return $comment->load(['task', 'author']);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->noContent();
    }

    public function taskComments($taskId)
    {
        $comments = Comment::where('task_id', $taskId)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($comments);
    }
}
