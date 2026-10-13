<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');
        $topicId = $request->query('topic_id');

        $query = Post::with(['user', 'topic'])->latest();

        if ($search) {
            $query->where('body', 'like', '%' . $search . '%');
        }

        if ($topicId) {
            $query->where('topic_id', $topicId);
        }

        return response()->json($query->paginate($perPage > 0 ? $perPage : 15));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
            'topic_id' => ['required', 'exists:topics,id'],
        ]);

        $post = Post::create([
            'body' => $data['body'],
            'topic_id' => $data['topic_id'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($post->load(['user', 'topic']), 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json($post->load(['user', 'topic']));
    }

    public function update(Request $request, Post $post): JsonResponse
    {
       if (
    $post->user_id !== $request->user()->id &&
    !in_array($request->user()->role, ['moderator', 'admin'])
) {
    return response()->json([
        'message' => 'Nemate dozvolu za ovu akciju.'
    ], 403);
}

        $data = $request->validate([
            'body' => ['sometimes', 'required', 'string'],
            'topic_id' => ['sometimes', 'required', 'exists:topics,id'],
        ]);

        $payload = [];

        if (isset($data['body'])) {
            $payload['body'] = $data['body'];
        }

        if (isset($data['topic_id'])) {
            $payload['topic_id'] = $data['topic_id'];
        }

        $post->update($payload);

        return response()->json($post->fresh()->load(['user', 'topic']));
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        if (
    $post->user_id !== $request->user()->id &&
    !in_array($request->user()->role, ['moderator', 'admin'])
) {
    return response()->json([
        'message' => 'Nemate dozvolu za ovu akciju.'
    ], 403);
}

        $post->delete();

        return response()->json(null, 204);
    }

    public function filter(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $topicId = $request->query('topic_id');

        $query = Post::with(['user', 'topic'])->latest();

        if ($search) {
            $query->where('body', 'like', '%' . $search . '%');
        }

        if ($topicId) {
            $query->where('topic_id', $topicId);
        }

        return response()->json($query->paginate(10));
    }

    public function export(): JsonResponse
    {
        $posts = Post::with(['user', 'topic'])->get();

        $csv = "id,body,topic_id,user_id,created_at\n";

        foreach ($posts as $post) {
            $csv .= implode(',', [
                $post->id,
                '"' . str_replace('"', '""', $post->body) . '"',
                $post->topic_id,
                $post->user_id,
                $post->created_at,
            ]) . "\n";
        }

        return response()->json([
            'format' => 'csv',
            'data' => $csv,
        ]);
    }
}
