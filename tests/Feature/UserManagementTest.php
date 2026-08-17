<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_promote_a_user_to_moderator(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/users/{$user->id}/role", [
            'role' => 'moderator',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'moderator']);
    }

    public function test_regular_user_cannot_change_roles(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $other = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/users/{$other->id}/role", [
            'role' => 'admin',
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_upload_an_avatar(): void
    {
        Storage::fake('public', ['url' => '/storage']);

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertOk();
        $this->assertNotNull($response->json('data.avatar_url'));

        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar_path);
    }
}
