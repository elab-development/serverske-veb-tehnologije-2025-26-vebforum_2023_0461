<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{

public function posts(Request $request, Topic $topic): JsonResponse
{
    $user = $request->user('sanctum');

    $posts = $topic->posts()
        ->with('user')
        ->withCount('likes')
        ->latest()
        ->get();

    if ($user) {
        $likedPostIds = $user->likes()
            ->whereIn('post_id', $posts->pluck('id'))
            ->pluck('post_id');

        $posts->each(function ($post) use ($likedPostIds) {
            $post->setAttribute('liked_by_user', $likedPostIds->contains($post->id));
        });
    } else {
        $posts->each->setAttribute('liked_by_user', false);
    }

    return response()->json($posts);
}
 public function index(Request $request): JsonResponse
{
    $perPage = (int) $request->query('per_page', 15);
    $search = $request->query('search');

    $sortBy = $request->query('sort_by', 'created_at');
    $sortOrder = $request->query('sort_order', 'desc');

    $allowedSorts = ['title', 'created_at'];

    if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'created_at';
    }

    if (!in_array($sortOrder, ['asc', 'desc'])) {
        $sortOrder = 'desc';
    }

    $query = Topic::with('user');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%');
        });
    }

    $query->orderBy($sortBy, $sortOrder);

    return response()->json(
        $query->paginate($perPage > 0 ? $perPage : 15)
    );
}

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateTopic($request);

        $topic = Topic::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($topic->load('user'), 201);
    }

    public function show(Topic $topic): JsonResponse
    {
        return response()->json($topic->load('user'));
    }

   public function update(Request $request, Topic $topic): JsonResponse
{
    if (
    $topic->user_id !== $request->user()->id &&
    !in_array($request->user()->role, ['moderator', 'admin'])
) {
    return response()->json([
        'message' => 'Nemate dozvolu za ovu akciju.'
    ], 403);
}

    $data = $this->validateTopic($request, $topic);

    $topic->update([
        'title' => $data['title'],
        'body' => $data['body'],
    ]);

    return response()->json($topic->fresh()->load('user'));
}

  public function destroy(Request $request, Topic $topic): JsonResponse
{
    if (
    $topic->user_id !== $request->user()->id &&
    !in_array($request->user()->role, ['moderator', 'admin'])
) {
    return response()->json([
        'message' => 'Nemate dozvolu za ovu akciju.'
    ], 403);
}

    $topic->delete();

    return response()->json(null, 204);
}

    protected function validateTopic(Request $request, ?Topic $topic = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
        ];

        $data = $request->validate($rules);

        return $data;
    }

    public function filter(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $userId = $request->query('user_id');

        $query = Topic::with('user')->latest();

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return response()->json($query->paginate(10));
    }

    public function export(): JsonResponse
    {
        $topics = Topic::with('user')->get();

        $csv = "id,title,body,user_id,created_at\n";

        foreach ($topics as $topic) {
            $csv .= implode(',', [
                $topic->id,
                '"' . str_replace('"', '""', $topic->title) . '"',
                '"' . str_replace('"', '""', $topic->body) . '"',
                $topic->user_id,
                $topic->created_at,
            ]) . "\n";
        }

        return response()->json([
            'format' => 'csv',
            'data' => $csv,
        ]);
    }
}