<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_topics_but_cannot_create_them(): void
    {
        $response = $this->getJson('/api/topics');
        $response->assertOk();

        $response = $this->postJson('/api/topics', [
            'title' => 'Example title',
            'body' => 'Example body',
            'category_id' => Category::factory()->create()->id,
        ]);

        $response->assertUnauthorized();
    }

    public function test_regular_user_cannot_update_another_users_topic(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $topic = Topic::factory()->create([
            'user_id' => $owner->id,
            'category_id' => Category::factory()->create()->id,
        ]);
        $otherUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($otherUser, 'sanctum')->putJson("/api/topics/{$topic->id}", [
            'title' => 'Updated title',
            'body' => 'Updated body',
            'category_id' => $topic->category_id,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_create_categories(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/categories', [
            'name' => 'Announcements',
            'description' => 'Admin only area',
        ]);

        $response->assertCreated();
    }
}
