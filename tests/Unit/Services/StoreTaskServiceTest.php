<?php

namespace Tests\Unit\Services;

use App\Http\Requests\CreateTaskRequest;
use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use App\Services\StoreTaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StoreTaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private StoreTaskService $storeTaskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storeTaskService = new StoreTaskService();
    }

    /**
     * Test creating a task.
     */
    public function test_can_execute_to_create_task(): void
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();

        Auth::shouldReceive('id')->andReturn($user->id);

        $taskData = [
            'building_id' => $building->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'open',
        ];

        $request = $this->mock(CreateTaskRequest::class);
        $request->shouldReceive('validated')->andReturn($taskData);

        $task = $this->storeTaskService->execute($request);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals($user->id, $task->created_by);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'created_by' => $user->id,
        ]);
    }
}

