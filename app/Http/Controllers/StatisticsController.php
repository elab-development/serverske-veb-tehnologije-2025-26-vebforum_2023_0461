<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function topics(): JsonResponse
    {
        $statistics = DB::table('topics')
            ->join('users', 'topics.user_id', '=', 'users.id')
            ->leftJoin('posts', 'topics.id', '=', 'posts.topic_id')
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
            ->select(
                'topics.id as topic_id',
                'topics.title',
                'users.name as author',
                DB::raw('COUNT(DISTINCT posts.id) as posts_count'),
                DB::raw('COUNT(DISTINCT comments.id) as comments_count'),
                DB::raw('COUNT(DISTINCT likes.id) as likes_count')
            )
            ->groupBy(
                'topics.id',
                'topics.title',
                'users.name'
            )
            ->orderByDesc('posts_count')
            ->get();

        return response()->json($statistics);
    }
}