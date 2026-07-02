<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_vote_and_update_existing_vote(): void
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'user_id' => $user->id,
        ]);

        $firstResponse = $this->actingAs($user, 'sanctum')->postJson("/api/topics/{$topic->id}/vote", [
            'value' => 1,
        ]);

        $firstResponse->assertOk();
        $firstResponse->assertJsonPath('topic_id', $topic->id);
        $firstResponse->assertJsonPath('total_score', '1');

        $secondResponse = $this->actingAs($user, 'sanctum')->postJson("/api/topics/{$topic->id}/vote", [
            'value' => -1,
        ]);

        $secondResponse->assertOk();
        $secondResponse->assertJsonPath('topic_id', $topic->id);
        $secondResponse->assertJsonPath('total_score', '-1');
    }
}
