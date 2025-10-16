<?php

namespace App\Services;

use App\Http\Requests\CreateTaskRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class StoreTaskService
{
    /**
     * Execute the service to create a new task.
     *
     * @param CreateTaskRequest $request
     * @return Task
     */
    public function execute(CreateTaskRequest $request): Task
    {
        return Task::create([
            ...$request->validated(),
            'created_by' => Auth::id()
        ]);
    }
}

