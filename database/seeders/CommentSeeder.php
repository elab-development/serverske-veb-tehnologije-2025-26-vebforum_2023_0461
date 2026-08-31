<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $userIds = User::pluck('id');

        Post::all()->each(function (Post $post) use ($userIds) {
            Comment::factory(2)->create([
                'post_id' => $post->id,
                'user_id' => $userIds->random(),
            ]);
        });
    }
}
