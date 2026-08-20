<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TopicVoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'vector.profanity.dev' => Http::response(['isProfanity' => false], 200),
        ]);
    }

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
        $this->assertEquals(1, $firstResponse->json('total_score'));

        $secondResponse = $this->actingAs($user, 'sanctum')->postJson("/api/topics/{$topic->id}/vote", [
            'value' => -1,
        ]);

        $secondResponse->assertOk();
        $secondResponse->assertJsonPath('topic_id', $topic->id);
        $this->assertEquals(-1, $secondResponse->json('total_score'));
    }
}
