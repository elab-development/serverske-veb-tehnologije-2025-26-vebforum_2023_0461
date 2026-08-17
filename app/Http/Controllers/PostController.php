<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Post::with(['user', 'topic'])->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
            'topic_id' => ['required', 'exists:topics,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $post = Post::create($data);

        return response()->json($post->load(['user', 'topic']), 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json($post->load(['user', 'topic']));
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $data = $request->validate([
            'body' => ['sometimes', 'required', 'string'],
            'topic_id' => ['sometimes', 'required', 'exists:topics,id'],
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
        ]);

        $post->update($data);

        return response()->json($post->fresh()->load(['user', 'topic']));
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
