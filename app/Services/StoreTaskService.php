<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class StoreTaskService
{
    /**
     * Execute the service to create a new task.
     *
     * @param array<string, mixed> $data
     * @return Task
     */
    public function execute(array $data): Task
    {
        $data['created_by'] = Auth::id();

        return Task::create($data);
    }
}

