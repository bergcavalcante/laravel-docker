<?php

namespace App\Services;

use App\Http\Requests\CreateCommentRequest;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Auth;

class StoreCommentService
{
    /**
     * Execute the service to create a new comment for a task.
     *
     * @param CreateCommentRequest $request
     * @return TaskComment
     */
    public function execute(CreateCommentRequest $request): TaskComment
    {
        return TaskComment::create([
            ...$request->validated(),
            'created_by' => Auth::id()
        ]);
    }
}

