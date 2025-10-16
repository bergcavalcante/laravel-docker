<?php

namespace Tests\Unit\Services;

use App\Http\Requests\CreateCommentRequest;
use App\Models\TaskComment;
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

        $request = $this->mock(CreateCommentRequest::class);
        $request->shouldReceive('validated')->andReturn($commentData);

        $comment = $this->storeCommentService->execute($request);

        $this->assertInstanceOf(TaskComment::class, $comment);
        $this->assertEquals('This is a test comment', $comment->content);
        $this->assertEquals($user->id, $comment->created_by);
        $this->assertEquals($task->id, $comment->task_id);
        $this->assertDatabaseHas('task_comments', [
            'content' => 'This is a test comment',
            'created_by' => $user->id,
            'task_id' => $task->id,
        ]);
    }
}

