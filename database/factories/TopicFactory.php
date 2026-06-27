<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition()
{
    return [
        'title' => fake()->sentence(4),
        'body' => fake()->paragraph(),
        'category_id' => \App\Models\Category::factory(),
        'user_id' => \App\Models\User::factory(),
    ];
}
}
