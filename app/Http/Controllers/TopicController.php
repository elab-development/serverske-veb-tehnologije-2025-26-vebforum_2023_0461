<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Topic::with('user')->latest()->get());
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
        $data = $this->validateTopic($request, $topic);

        $topic->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($topic->fresh()->load('user'));
    }

    public function destroy(Topic $topic): JsonResponse
    {
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
}
