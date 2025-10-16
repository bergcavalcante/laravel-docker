<?php

namespace Tests\Feature\Api;

use App\Models\Building;
use App\Models\TaskComment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing tasks for a building.
     */
    public function test_can_list_building_tasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $building = Building::factory()->create();
        $tasks = Task::factory()->count(3)->create(['building_id' => $building->id]);

        $response = $this->getJson("/api/buildings/{$building->id}/tasks");

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'building_id',
                        'title',
                        'description',
                        'status',
                        'due_date',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /**
     * Test filtering tasks by status.
     */
    public function test_can_filter_tasks_by_status(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $building = Building::factory()->create();
        Task::factory()->create(['building_id' => $building->id, 'status' => 'open']);
        Task::factory()->create(['building_id' => $building->id, 'status' => 'completed']);
        Task::factory()->create(['building_id' => $building->id, 'status' => 'open']);

        $response = $this->getJson("/api/buildings/{$building->id}/tasks?status=open");

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $task) {
            $this->assertEquals('open', $task['status']);
        }
    }

    /**
     * Test creating a task.
     */
    public function test_can_create_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $building = Building::factory()->create();
        $assignee = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'building_id' => $building->id,
            'title' => 'Fix broken window',
            'description' => 'Window on 3rd floor needs repair',
            'status' => 'open',
            'assigned_to' => $assignee->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Fix broken window')
            ->assertJsonPath('data.status', 'open');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Fix broken window',
            'building_id' => $building->id,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Test creating a task requires authentication.
     */
    public function test_creating_task_requires_authentication(): void
    {
        $building = Building::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'building_id' => $building->id,
            'title' => 'Fix broken window',
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Test task validation.
     */
    public function test_task_validation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tasks', [
            'title' => '', // Empty title
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['building_id', 'title']);
    }

    /**
     * Test filtering tasks by multiple criteria.
     */
    public function test_can_filter_tasks_by_multiple_criteria(): void
    {
        $user = User::factory()->create();
        $assignee = User::factory()->create();
        Sanctum::actingAs($user);

        $building = Building::factory()->create();
        
        Task::factory()->create([
            'building_id' => $building->id,
            'status' => 'open',
            'assigned_to' => $assignee->id,
            'created_at' => now()->subDays(2),
        ]);
        
        Task::factory()->create([
            'building_id' => $building->id,
            'status' => 'completed',
            'assigned_to' => $assignee->id,
            'created_at' => now()->subDays(2),
        ]);
        
        Task::factory()->create([
            'building_id' => $building->id,
            'status' => 'open',
            'assigned_to' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->getJson("/api/buildings/{$building->id}/tasks?status=open&assigned_to={$assignee->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test tasks include comments when loaded.
     */
    public function test_tasks_include_comments(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $building = Building::factory()->create();
        $task = Task::factory()->create(['building_id' => $building->id]);
        TaskComment::factory()->count(2)->create(['task_id' => $task->id]);

        $response = $this->getJson("/api/buildings/{$building->id}/tasks");

        $response->assertOk()
            ->assertJsonCount(2, 'data.0.comments');
    }
}

