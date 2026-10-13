<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_endpoint_aggregates_posts_comments_and_votes_per_topic(): void
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create([ 'user_id' => $user->id]);
        $post = Post::factory()->create(['topic_id' => $topic->id, 'user_id' => $user->id]);
        Post::factory()->create(['topic_id' => $topic->id, 'user_id' => $user->id]);


        $response = $this->getJson('/api/statistics/topics');

        $response->assertOk();
        $row = collect($response->json('data'))->firstWhere('id', $topic->id);

        $this->assertNotNull($row);
        $this->assertEquals(2, $row['posts_count']);
        $this->assertEquals(2, $row['score']);
    }
}
