<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class StoreCommentService
{
    /**
     * Execute the service to create a new comment for a task.
     *
     * @param array<string, mixed> $data
     * @return Comment
     */
    public function execute(array $data): Comment
    {
        $data['user_id'] = Auth::id();

        return Comment::create($data);
    }
}

