<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class GetTasksForBuildingService
{
    /**
     * Execute the service to get filtered tasks for a building with eager loaded relationships.
     *
     * @param int $buildingId
     * @param array<string, mixed> $filters
     * @return Collection<int, Task>
     */
    public function execute(int $buildingId, array $filters): Collection
    {
        $query = Task::with(['comments.user', 'assignee', 'creator'])
            ->where('building_id', $buildingId);

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by assigned user
        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Filter by creation date range
        if (isset($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // Filter by due date range
        if (isset($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        return $query->latest()->get();
    }
}

