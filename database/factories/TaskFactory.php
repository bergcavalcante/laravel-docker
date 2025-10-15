<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'building_id' => \App\Models\Building::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'assigned_to' => \App\Models\User::factory(),
            'created_by' => \App\Models\User::factory(),
            'status' => fake()->randomElement(['open', 'in_progress', 'completed', 'rejected']),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }

    /**
     * Indicate that the task is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
