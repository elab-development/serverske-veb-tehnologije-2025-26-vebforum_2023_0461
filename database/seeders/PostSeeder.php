<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id');

        Topic::all()->each(function (Topic $topic) use ($userIds) {
            Post::factory(4)->create([
                'topic_id' => $topic->id,
                'user_id' => $userIds->random(),
            ]);
        });
    }
}
