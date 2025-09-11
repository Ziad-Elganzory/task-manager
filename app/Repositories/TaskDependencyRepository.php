<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskDependency;

class TaskDependencyRepository
{
    public function __construct(protected TaskDependency $taskDependencyModel){}
    // List all dependencies for a task
    public function allForTask(Task $task)
    {
        return $task->dependencies;
    }

    public function create(Task $task, $dependsOnTaskId)
    {
        $task->dependencies()->syncWithoutDetaching([$dependsOnTaskId]);
        return $task->dependencies()->find($dependsOnTaskId);
    }

    public function delete(Task $task, $dependsOnTaskId)
    {
        $task->dependencies()->detach($dependsOnTaskId);
    }

    public function dependencyExists(Task $task, $dependsOnTaskId)
    {
        return $task->dependencies()->where('tasks.id', $dependsOnTaskId)->exists();
    }
}
