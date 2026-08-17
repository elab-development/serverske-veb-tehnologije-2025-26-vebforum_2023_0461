<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->filled('body')) {
            $query->where('body', 'like', '%' . $request->input('body') . '%');
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->input('topic_id'));
        }

        $sortable = ['created_at', 'updated_at'];
        $sortParam = (string) $request->input('sort', '-created_at');
        $sort = ltrim($sortParam, '-');
        $direction = str_starts_with($sortParam, '-') ? 'desc' : 'asc';

        if (in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $direction);
        }

        return PostResource::collection($query->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json([
            'message' => 'Create post form'
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
            'body' => 'required|string',
            'topic_id' => 'required|exists:topics,id',
        ]);

        if ($profanityFilter->containsProfanity($data['body'])) {
            return response()->json([
                'message' => 'Post content was rejected by the profanity filter.',
            ], 422);
        }

        $data['user_id'] = $request->user()->id;

        $post = Post::create($data);

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return response()->json([
            'message' => 'Edit post form'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'body' => 'required|string',
            'topic_id' => 'required|exists:topics,id',
        ]);

        $post->update($data);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
        return response()->json([
            'message' => 'Post deleted'
        ]);
    }
}
