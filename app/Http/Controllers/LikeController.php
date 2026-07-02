<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function togglePostLike(Request $request, Post $post)
    {
        $like = Like::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->whereNull('comment_id')
            ->first();

        if ($like) {
            $like->delete();

            return response()->json([
                'liked' => false,
                'post_id' => $post->id,
            ]);
        }

        Like::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'comment_id' => null,
        ]);

        return response()->json([
            'liked' => true,
            'post_id' => $post->id,
        ]);
    }

    public function toggleCommentLike(Request $request, Comment $comment)
    {
        $like = Like::where('user_id', $request->user()->id)
            ->where('comment_id', $comment->id)
            ->whereNull('post_id')
            ->first();

        if ($like) {
            $like->delete();

            return response()->json([
                'liked' => false,
                'comment_id' => $comment->id,
            ]);
        }

        Like::create([
            'user_id' => $request->user()->id,
            'post_id' => null,
            'comment_id' => $comment->id,
        ]);

        return response()->json([
            'liked' => true,
            'comment_id' => $comment->id,
        ]);
    }
}
