<?php

namespace Tests\Unit\Services;

use App\Http\Requests\TaskFilterRequest;
use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use App\Services\GetTasksForBuildingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTasksForBuildingServiceTest extends TestCase
{
    use RefreshDatabase;

    private GetTasksForBuildingService $getTasksForBuildingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->getTasksForBuildingService = new GetTasksForBuildingService();
    }

    /**
     * Create a mock TaskFilterRequest with given data.
     */
    private function createMockRequest(array $data): TaskFilterRequest
    {
        $request = $this->mock(TaskFilterRequest::class);
        $request->shouldReceive('validated')->andReturn($data);
        return $request;
    }

    /**
     * Test filtering tasks by status.
     */
    public function test_can_execute_to_filter_tasks_by_status(): void
    {
        $building = Building::factory()->create();
        Task::factory()->create(['building_id' => $building->id, 'status' => 'open']);
        Task::factory()->create(['building_id' => $building->id, 'status' => 'completed']);
        Task::factory()->create(['building_id' => $building->id, 'status' => 'open']);

        $request = $this->createMockRequest(['status' => 'open']);
        $tasks = $this->getTasksForBuildingService->execute($building, $request);

        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->every(fn ($task) => $task->status === 'open'));
    }

    /**
     * Test filtering tasks by assigned user.
     */
    public function test_can_execute_to_filter_tasks_by_assigned_user(): void
    {
        $building = Building::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Task::factory()->count(3)->create([
            'building_id' => $building->id,
            'assigned_to' => $user1->id,
        ]);
        Task::factory()->count(2)->create([
            'building_id' => $building->id,
            'assigned_to' => $user2->id,
        ]);

        $request = $this->createMockRequest(['assigned_to' => $user1->id]);
        $tasks = $this->getTasksForBuildingService->execute($building, $request);

        $this->assertCount(3, $tasks);
        $this->assertTrue($tasks->every(fn ($task) => $task->assigned_to === $user1->id));
    }

    /**
     * Test filtering tasks by date range.
     */
    public function test_can_execute_to_filter_tasks_by_date_range(): void
    {
        $building = Building::factory()->create();
        $oldTask = Task::factory()->create([
            'building_id' => $building->id,
            'created_at' => now()->subDays(10),
        ]);
        $recentTask = Task::factory()->create([
            'building_id' => $building->id,
            'created_at' => now()->subDays(2),
        ]);

        $request = $this->createMockRequest([
            'created_from' => now()->subDays(5)->format('Y-m-d'),
        ]);
        $tasks = $this->getTasksForBuildingService->execute($building, $request);

        $this->assertCount(1, $tasks);
        $this->assertEquals($recentTask->id, $tasks->first()->id);
    }

    /**
     * Test filtering tasks by multiple criteria.
     */
    public function test_can_execute_to_filter_tasks_by_multiple_criteria(): void
    {
        $building = Building::factory()->create();
        $user = User::factory()->create();

        Task::factory()->create([
            'building_id' => $building->id,
            'status' => 'open',
            'assigned_to' => $user->id,
            'created_at' => now()->subDays(2),
        ]);
        Task::factory()->create([
            'building_id' => $building->id,
            'status' => 'completed',
            'assigned_to' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        $request = $this->createMockRequest([
            'status' => 'open',
            'assigned_to' => $user->id,
        ]);
        $tasks = $this->getTasksForBuildingService->execute($building, $request);

        $this->assertCount(1, $tasks);
        $this->assertEquals('open', $tasks->first()->status);
    }

    /**
     * Test filtering tasks by due date range.
     */
    public function test_can_execute_to_filter_tasks_by_due_date_range(): void
    {
        $building = Building::factory()->create();
        
        Task::factory()->create([
            'building_id' => $building->id,
            'due_date' => now()->addDays(5),
        ]);
        Task::factory()->create([
            'building_id' => $building->id,
            'due_date' => now()->addDays(15),
        ]);

        $request = $this->createMockRequest([
            'due_date_from' => now()->format('Y-m-d'),
            'due_date_to' => now()->addDays(10)->format('Y-m-d'),
        ]);
        $tasks = $this->getTasksForBuildingService->execute($building, $request);

        $this->assertCount(1, $tasks);
    }
}

