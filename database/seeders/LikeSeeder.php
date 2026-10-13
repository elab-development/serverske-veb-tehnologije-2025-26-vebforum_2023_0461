<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    public function run()
    {
        $userIds = User::pluck('id');

        Post::all()->each(function (Post $post) use ($userIds) {
            foreach ($userIds->random(min(3, $userIds->count())) as $userId) {
                Like::firstOrCreate([
                    'user_id' => $userId,
                    'post_id' => $post->id,
                    'comment_id' => null,
                ]);
            }
        });

        Comment::all()->each(function (Comment $comment) use ($userIds) {
            foreach ($userIds->random(min(2, $userIds->count())) as $userId) {
                Like::firstOrCreate([
                    'user_id' => $userId,
                    'post_id' => null,
                    'comment_id' => $comment->id,
                ]);
            }
        });
    }
}
