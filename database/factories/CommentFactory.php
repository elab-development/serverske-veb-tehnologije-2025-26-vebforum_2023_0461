<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = \App\Models\Comment::class;

    public function definition()
    {
        return [
            'body' => fake()->sentence(),
            'user_id' => \App\Models\User::factory(),
            'post_id' => \App\Models\Post::factory(),
        ];
    }
}
