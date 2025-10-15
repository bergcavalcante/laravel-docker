<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test owner account
        $owner = User::factory()->owner()->create([
            'name' => 'John Doe',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create test team members
        $teamMember1 = User::factory()->teamMember()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'account_id' => $owner->id,
        ]);

        $teamMember2 = User::factory()->teamMember()->create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'account_id' => $owner->id,
        ]);

        // Create more team members
        $additionalTeamMembers = User::factory()
            ->count(5)
            ->teamMember()
            ->create(['account_id' => $owner->id]);

        // Create buildings
        $building1 = Building::factory()->create([
            'account_id' => $owner->id,
            'name' => 'Downtown Office Building',
            'address' => '123 Main St, New York, NY 10001',
        ]);

        $building2 = Building::factory()->create([
            'account_id' => $owner->id,
            'name' => 'Residential Complex',
            'address' => '456 Park Ave, Los Angeles, CA 90001',
        ]);

        $building3 = Building::factory()->create([
            'account_id' => $owner->id,
            'name' => 'Shopping Mall',
            'address' => '789 Commerce Blvd, Chicago, IL 60601',
        ]);

        // Create tasks for building 1
        $task1 = Task::factory()->open()->create([
            'building_id' => $building1->id,
            'title' => 'Fix broken window on 3rd floor',
            'description' => 'The window in office 305 needs to be replaced',
            'assigned_to' => $teamMember1->id,
            'created_by' => $owner->id,
            'due_date' => now()->addDays(7),
        ]);

        $task2 = Task::factory()->inProgress()->create([
            'building_id' => $building1->id,
            'title' => 'Repair elevator in main lobby',
            'description' => 'Elevator #2 is making strange noises',
            'assigned_to' => $teamMember2->id,
            'created_by' => $owner->id,
            'due_date' => now()->addDays(3),
        ]);

        // Create tasks for building 2
        Task::factory()->count(10)->create([
            'building_id' => $building2->id,
            'created_by' => $owner->id,
            'assigned_to' => $additionalTeamMembers->random()->id,
        ]);

        // Create tasks for building 3
        Task::factory()->count(8)->create([
            'building_id' => $building3->id,
            'created_by' => $owner->id,
            'assigned_to' => $additionalTeamMembers->random()->id,
        ]);

        // Create comments for tasks
        Comment::factory()->create([
            'task_id' => $task1->id,
            'user_id' => $teamMember1->id,
            'content' => 'I have assessed the damage and ordered the replacement window.',
        ]);

        Comment::factory()->create([
            'task_id' => $task1->id,
            'user_id' => $owner->id,
            'content' => 'Great! Please keep me updated on the delivery date.',
        ]);

        Comment::factory()->create([
            'task_id' => $task2->id,
            'user_id' => $teamMember2->id,
            'content' => 'Started inspection. Found a loose cable that needs replacement.',
        ]);

        // Create random comments for other tasks
        Task::all()->each(function ($task) use ($owner, $teamMember1, $teamMember2) {
            if (rand(0, 1)) {
                Comment::factory()->count(rand(1, 3))->create([
                    'task_id' => $task->id,
                    'user_id' => collect([$owner, $teamMember1, $teamMember2])->random()->id,
                ]);
            }
        });
    }
}
