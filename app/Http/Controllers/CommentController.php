<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        return CommentResource::collection($post->comments()->with('user')->latest()->get());
    }

    public function store(Request $request, Post $post, ProfanityFilter $profanityFilter)
    {
        $data = $request->validate([
            'body' => 'required|string',
        ]);

        if ($profanityFilter->containsProfanity($data['body'])) {
            return response()->json([
                'message' => 'Comment content was rejected by the profanity filter.',
            ], 422);
        }

        $comment = Comment::create([
            'body' => $data['body'],
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return (new CommentResource($comment->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $comment->update($data);

        return new CommentResource($comment);
    }

    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
