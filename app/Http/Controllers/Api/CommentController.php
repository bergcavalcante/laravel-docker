<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\StoreCommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param StoreCommentService $storeCommentService
     */
    public function __construct(
        private readonly StoreCommentService $storeCommentService
    ) {
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
        $comment = $this->storeCommentService->execute($request->validated());

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}
