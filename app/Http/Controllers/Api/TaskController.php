<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\TaskFilterRequest;
use App\Http\Resources\TaskResource;
use App\Services\GetTasksForBuildingService;
use App\Services\StoreTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param GetTasksForBuildingService $getTasksForBuildingService
     * @param StoreTaskService $storeTaskService
     */
    public function __construct(
        private readonly GetTasksForBuildingService $getTasksForBuildingService,
        private readonly StoreTaskService $storeTaskService
    ) {
    }

    /**
     * Get all tasks for a building with optional filters.
     *
     * This endpoint retrieves tasks for a specific building, including related comments
     * and user information. Supports multiple filter options such as status, assigned user,
     * and date ranges.
     *
     * @param int $buildingId The ID of the building
     * @param TaskFilterRequest $request The validated filter request
     * @return AnonymousResourceCollection Collection of task resources
     */
    public function index(int $buildingId, TaskFilterRequest $request): AnonymousResourceCollection
    {
        $tasks = $this->getTasksForBuildingService->execute($buildingId, $request->validated());

        return TaskResource::collection($tasks);
    }

    /**
     * Create a new task.
     *
     * This endpoint creates a new task with the provided information. The task
     * is automatically associated with the authenticated user as the creator.
     *
     * @param CreateTaskRequest $request The validated task creation request
     * @return JsonResponse The created task resource with 201 status
     */
    public function store(CreateTaskRequest $request): JsonResponse
    {
        $task = $this->storeTaskService->execute($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }
}
