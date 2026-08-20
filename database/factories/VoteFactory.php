<?php

namespace Database\Factories;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'topic_id' => Topic::factory(),
            'value' => fake()->randomElement([1, -1]),
        ];
    }
}
