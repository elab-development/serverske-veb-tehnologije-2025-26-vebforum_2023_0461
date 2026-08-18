<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        return response()->json($post->comments()->with('user')->latest()->get());
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment = $post->comments()->create([
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $data = $request->validate([
            'body' => ['sometimes', 'required', 'string'],
        ]);

        $payload = [
            'user_id' => $request->user()->id,
        ];

        if (isset($data['body'])) {
            $payload['body'] = $data['body'];
        }

        $comment->update($payload);

        return response()->json($comment->fresh()->load('user'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(null, 204);
    }
}
