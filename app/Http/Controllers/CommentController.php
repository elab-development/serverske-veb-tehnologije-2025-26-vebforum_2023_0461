<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        return response()->json($post->comments()->with('user')->get());
    }

    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $comment = Comment::create([
            'body' => $data['body'],
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return response()->json($comment, 201);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($request->user()->role === 'moderator' || $request->user()->role === 'admin' || $comment->user_id === $request->user()->id) {
            $data = $request->validate([
                'body' => 'required|string',
            ]);

            $comment->update($data);

            return response()->json($comment);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($request->user()->role === 'moderator' || $request->user()->role === 'admin' || $comment->user_id === $request->user()->id) {
            $comment->delete();

            return response()->json(['message' => 'Comment deleted']);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
