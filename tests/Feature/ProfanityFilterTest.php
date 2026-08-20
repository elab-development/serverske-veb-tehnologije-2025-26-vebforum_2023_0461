<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProfanityFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_topic_creation_is_rejected_when_profanity_filter_flags_it(): void
    {
        Http::fake([
            'vector.profanity.dev' => Http::response(['isProfanity' => true], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/topics', [
            'title' => 'Bad title',
            'body' => 'Bad body',
            'category_id' => Category::factory()->create()->id,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('topics', ['title' => 'Bad title']);
    }

    public function test_topic_creation_succeeds_when_profanity_filter_service_is_unavailable(): void
    {
        Http::fake([
            'vector.profanity.dev' => Http::response(null, 500),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/topics', [
            'title' => 'Clean title',
            'body' => 'Clean body',
            'category_id' => Category::factory()->create()->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('topics', ['title' => 'Clean title']);
    }
}
