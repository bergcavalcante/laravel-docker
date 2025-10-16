<?php

namespace App\Services;

use App\Http\Requests\TaskFilterRequest;
use App\Models\Building;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class GetTasksForBuildingService
{
    /**
     * Execute the service to get filtered tasks for a building with eager loaded relationships.
     *
     * @param Building $building
     * @param TaskFilterRequest $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function execute(Building $building, TaskFilterRequest $request)
    {
        $buildingId = $building->id;
        $filters = $request->validated();

        $query = Task::with(['comments.user', 'assignee', 'creator'])
            ->where('building_id', $buildingId);

        $query = $this->applyFilters($query, $filters);

        return $query->paginate();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        if (isset($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        return $query;
    }
}

