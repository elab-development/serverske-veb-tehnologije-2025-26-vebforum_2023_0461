<?php

namespace App\Http\Controllers;

use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\Vote;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Topic::query()->withSum('votes', 'value');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $sortable = ['title', 'created_at', 'updated_at'];
        $sort = ltrim((string) $request->input('sort', '-created_at'), '-');
        $direction = str_starts_with((string) $request->input('sort', '-created_at'), '-') ? 'desc' : 'asc';

        if (in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $direction);
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
    public function store(Request $request, ProfanityFilter $profanityFilter)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($profanityFilter->containsProfanity($data['title'] . ' ' . $data['body'])) {
            return response()->json([
                'message' => 'Topic content was rejected by the profanity filter.',
            ], 422);
        }

        $data['user_id'] = $request->user()->id;
        $topic = Topic::create($data);

        return (new TopicResource($topic))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Topic $topic)
    {
        $topic->loadSum('votes', 'value');

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
        $this->authorize('update', $topic);

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
        $this->authorize('delete', $topic);

        DB::transaction(function () use ($topic) {
            foreach ($topic->posts as $post) {
                $post->likes()->delete();

                foreach ($post->comments as $comment) {
                    $comment->likes()->delete();
                    $comment->delete();
                }

                $post->delete();
            }

            $topic->votes()->delete();
            $topic->delete();
        });

        return response()->json([
            'message' => 'Topic deleted'
        ]);
    }

    public function vote(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'value' => 'required|integer|in:1,-1',
        ]);

        [$vote, $totalScore] = DB::transaction(function () use ($request, $topic, $data) {
            $vote = Vote::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'topic_id' => $topic->id,
                ],
                [
                    'value' => $data['value'],
                ]
            );

            return [$vote, $topic->votes()->sum('value')];
        });

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

    /**
     * Aggregated topic statistics: joins topics with per-topic post counts,
     * comment counts and vote score, each pre-aggregated in its own subquery
     * to avoid the row-multiplication that a single multi-table join would cause.
     */
    public function statistics()
    {
        $postCounts = DB::table('posts')
            ->select('topic_id', DB::raw('COUNT(*) as posts_count'))
            ->groupBy('topic_id');

        $commentCounts = DB::table('comments')
            ->join('posts', 'posts.id', '=', 'comments.post_id')
            ->select('posts.topic_id', DB::raw('COUNT(comments.id) as comments_count'))
            ->groupBy('posts.topic_id');

        $voteScores = DB::table('votes')
            ->select('topic_id', DB::raw('SUM(value) as score'))
            ->groupBy('topic_id');

        $topics = Topic::query()
            ->select('topics.id', 'topics.title', 'topics.category_id', 'categories.name as category_name')
            ->join('categories', 'categories.id', '=', 'topics.category_id')
            ->leftJoinSub($postCounts, 'post_counts', 'post_counts.topic_id', '=', 'topics.id')
            ->leftJoinSub($commentCounts, 'comment_counts', 'comment_counts.topic_id', '=', 'topics.id')
            ->leftJoinSub($voteScores, 'vote_scores', 'vote_scores.topic_id', '=', 'topics.id')
            ->selectRaw('COALESCE(post_counts.posts_count, 0) as posts_count')
            ->selectRaw('COALESCE(comment_counts.comments_count, 0) as comments_count')
            ->selectRaw('COALESCE(vote_scores.score, 0) as score')
            ->orderByDesc('score')
            ->limit(10)
            ->get();

        return response()->json(['data' => $topics]);
    }
}
