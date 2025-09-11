<?php

namespace App\Services;

use App\Exceptions\CompleteDependencyException;
use App\Exceptions\TaskCreationFailedException;
use App\Exceptions\TaskDeleteFailedException;
use App\Exceptions\TaskNotFoundException;
use App\Exceptions\TaskSoftDeleteFailedException;
use App\Repositories\TaskRepository;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(protected TaskRepository $taskRepository){}

    public function getAllTasks($filters = [])
    {
        $tasks = $this->taskRepository->all($filters);
        if($tasks->isEmpty()) {
            throw new TaskNotFoundException;
        }
        return $tasks;
    }

    public function getTask($id)
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            throw new TaskNotFoundException;
        }
        return $task;
    }

    public function createTask(array $data)
    {
        $task = $this->taskRepository->create($data);
        if (!$task) {
            throw new TaskCreationFailedException;
        }
        return $task;
    }


    public function updateTask(Task $task, array $data)
    {
        if (isset($data['status']) && $data['status'] === 'completed') {
            $incompleteDependencies = $task->dependencies()->where('status', '!=', 'completed')->count();
            if ($incompleteDependencies > 0) {
                throw new CompleteDependencyException;
            }
        }
        return $this->taskRepository->update($task, $data);
    }

    public function softDeleteTask(Task $task)
    {
        if (!$this->taskRepository->softDelete($task)) {
            throw new TaskSoftDeleteFailedException;
        }
        return true;
    }

    public function forceDeleteTask(Task $task)
    {
        $deletedTask = $this->taskRepository->forceDelete($task);
        if(!$deletedTask) {
            throw new TaskDeleteFailedException;
        }
        return true;
    }

    public function getTaskWithTrashed($id): ?Task
    {

        $task = $this->taskRepository->findWithTrashed($id);
        if(!$task) {
            throw new TaskNotFoundException;
        }
        return $task;
    }

}
