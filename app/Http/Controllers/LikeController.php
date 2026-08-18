<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $userId = $request->user()->id;

        $existingLike = $post->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json([
                'post_id' => $post->id,
                'user_id' => $userId,
                'liked' => false,
            ]);
        }

        $like = Like::create([
            'post_id' => $post->id,
            'user_id' => $userId,
        ]);

        return response()->json([
            'id' => $like->id,
            'post_id' => $post->id,
            'user_id' => $userId,
            'liked' => true,
        ]);
    }
}
