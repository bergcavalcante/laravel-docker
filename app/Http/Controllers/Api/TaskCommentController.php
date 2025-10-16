<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Resources\TaskCommentResource;
use App\Models\Task;
use App\Services\GetTaskCommentsService;
use App\Services\StoreCommentService;
use Illuminate\Http\JsonResponse;

class TaskCommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param GetTaskCommentsService $getTaskCommentsService
     * @param StoreCommentService $storeCommentService
     */
    public function __construct(
        private readonly GetTaskCommentsService $getTaskCommentsService,
        private readonly StoreCommentService $storeCommentService
    ) {
    }

    /**
     * Get all comments for a task.
     *
     * This endpoint retrieves paginated comments for a specific task, including
     * the user who created each comment.
     *
     * @param Task $task The task to get comments for
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection Collection of comment resources
     */
    public function index(Task $task)
    {
        $comments = $this->getTaskCommentsService->execute($task);

        return TaskCommentResource::collection($comments);
    }

    /**
     * Create a new comment for a task.
     *
     * This endpoint creates a new comment on an existing task. The comment
     * is automatically associated with the authenticated user.
     *
     * @param CreateCommentRequest $request The validated comment creation request
     * @return JsonResponse The created comment resource with 201 status
     */
    public function store(CreateCommentRequest $request): JsonResponse
    {
        $comment = $this->storeCommentService->execute($request);

        return (new TaskCommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}
