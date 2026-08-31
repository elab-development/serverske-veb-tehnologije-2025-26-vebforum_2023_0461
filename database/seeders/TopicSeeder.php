<?php

namespace Database\Seeders;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id');

        Topic::factory(10)->create()->each(function ($topic) use ($userIds) {
            $topic->update([
                'user_id' => $userIds->random(),
            ]);
        });
    }
}