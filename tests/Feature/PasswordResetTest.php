<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_and_use_a_password_reset_token(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);

        $forgotResponse = $this->postJson('/api/forgot-password', [
            'email' => 'reset@example.com',
        ]);

        $forgotResponse->assertOk();
        $token = $forgotResponse->json('reset_token');
        $this->assertNotEmpty($token);

        $resetResponse = $this->postJson('/api/reset-password', [
            'email' => 'reset@example.com',
            'token' => $token,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $resetResponse->assertOk();

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_reset_fails_with_invalid_token(): void
    {
        User::factory()->create(['email' => 'reset2@example.com']);

        $this->postJson('/api/forgot-password', ['email' => 'reset2@example.com']);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'reset2@example.com',
            'token' => 'wrong-token',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422);
    }
}
