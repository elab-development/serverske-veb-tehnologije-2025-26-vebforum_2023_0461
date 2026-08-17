<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id');

        Category::all()->each(function (Category $category) use ($userIds) {
            Topic::factory(3)->create([
                'category_id' => $category->id,
                'user_id' => $userIds->random(),
            ]);
        });
    }
}
