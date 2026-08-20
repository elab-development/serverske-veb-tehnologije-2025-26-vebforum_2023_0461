<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiscussionStarterTest extends TestCase
{
    use RefreshDatabase;

    public function test_discussion_starter_returns_processed_quote_data(): void
    {
        Http::fake([
            'dummyjson.com/*' => Http::response([
                'id' => 1,
                'quote' => 'Talk is cheap. Show me the code.',
                'author' => 'Linus Torvalds',
            ], 200),
        ]);

        $response = $this->getJson('/api/discussion-starter');

        $response->assertOk();
        $response->assertJsonPath('data.quote', 'Talk is cheap. Show me the code.');
        $response->assertJsonPath('data.author', 'Linus Torvalds');
        $response->assertJsonPath('data.word_count', 7);
    }

    public function test_discussion_starter_returns_503_when_service_is_down(): void
    {
        Http::fake([
            'dummyjson.com/*' => Http::response(null, 500),
        ]);

        $response = $this->getJson('/api/discussion-starter');

        $response->assertStatus(503);
    }
}
