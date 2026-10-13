<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'vector.profanity.dev' => Http::response(['isProfanity' => false], 200),
        ]);
    }

    public function test_guest_can_view_topics_but_cannot_create_them(): void
    {
        $response = $this->getJson('/api/topics');
        $response->assertOk();

        $response = $this->postJson('/api/topics', [
            'title' => 'Example title',
            'body' => 'Example body',
           
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthenticated_request_without_accept_header_still_gets_json_401(): void
    {
        // Regression test: the Authenticate middleware used to call route('login')
        // for any request that didn't send an explicit Accept: application/json
        // header, and this API has no named "login" route, so it crashed with a
        // 500 instead of a clean 401.
        $response = $this->post('/api/topics', [
            'title' => 'Example title',
            'body' => 'Example body',
        ]);

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_regular_user_cannot_update_another_users_topic(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $topic = Topic::factory()->create([
            'user_id' => $owner->id,
           
        ]);
        $otherUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($otherUser, 'sanctum')->putJson("/api/topics/{$topic->id}", [
            'title' => 'Updated title',
            'body' => 'Updated body',
            
        ]);

        $response->assertForbidden();
    }


}
