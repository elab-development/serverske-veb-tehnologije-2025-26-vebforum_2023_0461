<?php

namespace App\Http\Controllers;

use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\Vote;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Topic::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        return TopicResource::collection($query->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json([
            'message' => 'Create topic form'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = $request->user()->id;
        $topic = Topic::create($data);

        return new TopicResource($topic);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Topic $topic)
    {
        return new TopicResource($topic);
    }

    public function posts(Topic $topic)
    {
        return response()->json($topic->posts);
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'query' => 'required|string'
        ]);

        $topics = Topic::where('title', 'like', '%' . $data['query'] . '%')
            ->orWhere('body', 'like', '%' . $data['query'] . '%')
            ->get();

        return TopicResource::collection($topics);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function edit(Topic $topic)
    {
        return response()->json([
            'message' => 'Edit topic form'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topic $topic)
    {
        if (! in_array($request->user()->role, ['admin', 'moderator'], true) && $topic->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $topic->update($data);

        return new TopicResource($topic);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Topic $topic)
    {
        if (! in_array($request->user()->role, ['admin', 'moderator'], true) && $topic->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $topic->delete();
        return response()->json([
            'message' => 'Topic deleted'
        ]);
    }

    public function vote(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'value' => 'required|integer|in:1,-1',
        ]);

        $vote = Vote::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'topic_id' => $topic->id,
            ],
            [
                'value' => $data['value'],
            ]
        );

        $totalScore = $topic->votes()->sum('value');

        return response()->json([
            'topic_id' => $topic->id,
            'vote' => [
                'user_id' => $vote->user_id,
                'topic_id' => $vote->topic_id,
                'value' => $vote->value,
            ],
            'total_score' => $totalScore,
        ]);
    }
}
