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
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $existingLike = $post->likes()->where('user_id', $data['user_id'])->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json([
                'post_id' => $post->id,
                'user_id' => $data['user_id'],
                'liked' => false,
            ]);
        }

        $like = Like::create([
            'post_id' => $post->id,
            'user_id' => $data['user_id'],
        ]);

        return response()->json([
            'id' => $like->id,
            'post_id' => $post->id,
            'user_id' => $data['user_id'],
            'liked' => true,
        ]);
    }
}
