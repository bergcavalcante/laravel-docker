<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskCommentControllerIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing comments for a task.
     */
    public function test_can_list_task_comments(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->create();
        TaskComment::factory()->count(5)->create(['task_id' => $task->id]);

        $response = $this->getJson("/api/tasks/{$task->id}/comments");

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'task_id',
                        'content',
                        'user',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /**
     * Test comments are ordered by created_at descending.
     */
    public function test_comments_are_ordered_by_created_at_desc(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->create();
        
        // Create comments with different timestamps
        $comment1 = TaskComment::factory()->create([
            'task_id' => $task->id,
            'created_at' => now()->subDays(2),
        ]);
        
        $comment2 = TaskComment::factory()->create([
            'task_id' => $task->id,
            'created_at' => now()->subDays(1),
        ]);
        
        $comment3 = TaskComment::factory()->create([
            'task_id' => $task->id,
            'created_at' => now(),
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}/comments");

        $response->assertOk();
        
        $comments = $response->json('data');
        
        // Most recent comment should be first
        $this->assertEquals($comment3->id, $comments[0]['id']);
        $this->assertEquals($comment2->id, $comments[1]['id']);
        $this->assertEquals($comment1->id, $comments[2]['id']);
    }

    /**
     * Test listing comments requires authentication.
     */
    public function test_listing_comments_requires_authentication(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}/comments");

        $response->assertUnauthorized();
    }

    /**
     * Test listing comments for non-existent task.
     */
    public function test_listing_comments_for_non_existent_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/tasks/99999/comments");

        $response->assertNotFound();
    }

    /**
     * Test pagination works correctly.
     */
    public function test_comments_are_paginated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->create();
        TaskComment::factory()->count(20)->create(['task_id' => $task->id]);

        $response = $this->getJson("/api/tasks/{$task->id}/comments");

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);

        $meta = $response->json('meta');
        $this->assertEquals(20, $meta['total']);
        $this->assertGreaterThan(1, $meta['last_page']);
    }

    /**
     * Test comments include user information.
     */
    public function test_comments_include_user_information(): void
    {
        $user = User::factory()->create();
        $commentAuthor = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->create();
        $comment = TaskComment::factory()->create([
            'task_id' => $task->id,
            'created_by' => $commentAuthor->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}/comments");

        $response->assertOk()
            ->assertJsonPath('data.0.user.id', $commentAuthor->id)
            ->assertJsonPath('data.0.user.name', $commentAuthor->name);
    }

    /**
     * Test only comments for specified task are returned.
     */
    public function test_only_comments_for_specified_task_are_returned(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        
        TaskComment::factory()->count(3)->create(['task_id' => $task1->id]);
        TaskComment::factory()->count(5)->create(['task_id' => $task2->id]);

        $response = $this->getJson("/api/tasks/{$task1->id}/comments");

        $response->assertOk()
            ->assertJsonCount(3, 'data');

        foreach ($response->json('data') as $comment) {
            $this->assertEquals($task1->id, $comment['task_id']);
        }
    }
}

