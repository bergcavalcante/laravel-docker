<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Builder;

class GetTaskCommentsService
{
    /**
     * Execute the service to get paginated comments for a task with eager loaded relationships.
     *
     * @param Task $task
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function execute(Task $task)
    {
        $taskId = $task->id;

        $query = TaskComment::with(['user', 'task'])
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc');

        return $query->paginate();
    }
}

