<?php

namespace Tests\Unit\Services;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Services\StoreCommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StoreCommentServiceTest extends TestCase
{
    use RefreshDatabase;

    private StoreCommentService $storeCommentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storeCommentService = new StoreCommentService();
    }

    /**
     * Test creating a comment.
     */
    public function test_can_execute_to_create_comment(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        Auth::shouldReceive('id')->andReturn($user->id);

        $commentData = [
            'task_id' => $task->id,
            'content' => 'This is a test comment',
        ];

        $comment = $this->storeCommentService->execute($commentData);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('This is a test comment', $comment->content);
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($task->id, $comment->task_id);
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'user_id' => $user->id,
            'task_id' => $task->id,
        ]);
    }
}

