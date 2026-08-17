<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TopicSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            LikeSeeder::class,
            VoteSeeder::class,
        ]);
    }
}