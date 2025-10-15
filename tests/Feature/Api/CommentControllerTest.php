<?php

namespace Tests\Feature\Api;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a comment.
     */
    public function test_can_create_comment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->create();

        $response = $this->postJson('/api/comments', [
            'task_id' => $task->id,
            'content' => 'This is a test comment',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.content', 'This is a test comment')
            ->assertJsonPath('data.task_id', $task->id);

        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment',
        ]);
    }

    /**
     * Test creating a comment requires authentication.
     */
    public function test_creating_comment_requires_authentication(): void
    {
        $task = Task::factory()->create();

        $response = $this->postJson('/api/comments', [
            'task_id' => $task->id,
            'content' => 'This is a test comment',
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Test comment validation.
     */
    public function test_comment_validation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/comments', [
            'content' => '', // Empty content
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['task_id', 'content']);
    }

    /**
     * Test comment requires valid task.
     */
    public function test_comment_requires_valid_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/comments', [
            'task_id' => 99999, // Non-existent task
            'content' => 'This is a test comment',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['task_id']);
    }
}

