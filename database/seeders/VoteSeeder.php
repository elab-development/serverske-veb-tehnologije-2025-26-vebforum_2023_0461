<?php

namespace Database\Seeders;

use App\Models\Topic;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    public function run()
    {
        $userIds = User::pluck('id');

        Topic::all()->each(function (Topic $topic) use ($userIds) {
            foreach ($userIds->random(min(4, $userIds->count())) as $userId) {
                Vote::firstOrCreate(
                    ['user_id' => $userId, 'topic_id' => $topic->id],
                    ['value' => fake()->randomElement([1, -1])]
                );
            }
        });
    }
}
