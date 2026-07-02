<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_comments_and_likes(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'topic_id' => Topic::factory()->create([
                'user_id' => $author->id,
                'category_id' => Category::factory()->create()->id,
            ])->id,
        ]);

        $commentResponse = $this->actingAs($author, 'sanctum')->postJson("/api/posts/{$post->id}/comments", [
            'body' => 'Great post',
        ]);

        $commentResponse->assertCreated();

        $comment = Comment::latest()->first();

        $updateResponse = $this->actingAs($author, 'sanctum')->putJson("/api/comments/{$comment->id}", [
            'body' => 'Updated comment',
        ]);

        $updateResponse->assertOk();

        $likeResponse = $this->actingAs($author, 'sanctum')->postJson("/api/posts/{$post->id}/like");
        $likeResponse->assertOk();
        $likeResponse->assertJsonPath('liked', true);

        $toggleResponse = $this->actingAs($author, 'sanctum')->postJson("/api/posts/{$post->id}/like");
        $toggleResponse->assertOk();
        $toggleResponse->assertJsonPath('liked', false);
    }

    public function test_moderator_can_delete_any_comment(): void
    {
        $moderator = User::factory()->create(['role' => 'moderator']);
        $author = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'topic_id' => Topic::factory()->create([
                'user_id' => $author->id,
                'category_id' => Category::factory()->create()->id,
            ])->id,
        ]);
        $comment = Comment::factory()->create([
            'user_id' => $author->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($moderator, 'sanctum')->deleteJson("/api/comments/{$comment->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
