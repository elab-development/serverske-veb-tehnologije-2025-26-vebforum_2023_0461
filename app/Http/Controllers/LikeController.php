<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $userId = $request->user()->id;

        try {
            $result = DB::transaction(function () use ($post, $userId) {
                $existingLike = $post->likes()
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if ($existingLike) {
                    $existingLike->delete();

                    return [
                        'post_id' => $post->id,
                        'user_id' => $userId,
                        'liked' => false,
                    ];
                }

                $like = Like::create([
                    'post_id' => $post->id,
                    'user_id' => $userId,
                ]);

                return [
                    'id' => $like->id,
                    'post_id' => $post->id,
                    'user_id' => $userId,
                    'liked' => true,
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Greška pri lajkovanju posta.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
