<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Topic;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $categories = Category::factory(5)->create();

        $categories->each(function ($category) use ($admin, $user) {
            $topics = Topic::factory(3)->create([
                'category_id' => $category->id,
                'user_id' => $admin->id,
            ]);

            $topics->each(function ($topic) use ($user) {
                Post::factory(4)->create([
                    'topic_id' => $topic->id,
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}