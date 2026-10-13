<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_topics_api_endpoints(): void
    {
        $user = User::factory()->create();

        $listResponse = $this->getJson('/api/topics');
        $listResponse->assertOk();

        $createResponse = $this->postJson('/api/topics', [
            'title' => 'First topic',
            'body' => 'Topic description',
            'user_id' => $user->id,
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('title', 'First topic');

        $topic = Topic::first();

        $this->putJson('/api/topics/' . $topic->id, [
            'title' => 'Updated topic',
            'body' => 'Updated text',
            'user_id' => $user->id,
        ])->assertOk()->assertJsonPath('title', 'Updated topic');

        $this->deleteJson('/api/topics/' . $topic->id)->assertNoContent();
    }

    public function test_posts_and_comments_api_endpoints(): void
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'title' => 'Topic',
            'body' => 'Body',
            'user_id' => $user->id,
        ]);

        $postCreateResponse = $this->postJson('/api/posts', [
            'body' => 'Post body',
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);

        $postCreateResponse->assertStatus(201)
            ->assertJsonPath('body', 'Post body');

        $post = Post::first();

        $listCommentsResponse = $this->getJson('/api/posts/' . $post->id . '/comments');
        $listCommentsResponse->assertOk();

        $commentCreateResponse = $this->postJson('/api/posts/' . $post->id . '/comments', [
            'body' => 'Comment body',
            'user_id' => $user->id,
        ]);

        $commentCreateResponse->assertStatus(201)
            ->assertJsonPath('body', 'Comment body');

        $comment = Comment::first();

        $this->patchJson('/api/comments/' . $comment->id, [
            'body' => 'Updated comment',
            'user_id' => $user->id,
        ])->assertOk()->assertJsonPath('body', 'Updated comment');

        $this->deleteJson('/api/comments/' . $comment->id)->assertNoContent();
    }

    public function test_like_endpoint_toggles_status(): void
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'title' => 'Like topic',
            'body' => 'Topic body',
            'user_id' => $user->id,
        ]);
        $post = Post::create([
            'body' => 'Like me',
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);

        $firstLike = $this->postJson('/api/posts/' . $post->id . '/like', [
            'user_id' => $user->id,
        ]);

        $firstLike->assertOk()->assertJsonPath('liked', true);
        $this->assertDatabaseHas('likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $secondLike = $this->postJson('/api/posts/' . $post->id . '/like', [
            'user_id' => $user->id,
        ]);

        $secondLike->assertOk()->assertJsonPath('liked', false);
        $this->assertDatabaseMissing('likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }
}
