<?php

namespace App\Services;

use App\Exceptions\CircularDependencyException;
use App\Exceptions\TaskDependencyExistException;
use App\Exceptions\TaskDependencyNotFound;
use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use App\Repositories\TaskDependencyRepository;
use App\Repositories\TaskRepository;

class TaskDependencyService
{
    public function __construct(
        protected TaskDependencyRepository $taskDependencyRepository,
        protected TaskRepository $taskRepository
    ){}

    public function listDependencies($taskId)
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            throw new TaskNotFoundException;
        }
        $dependencies = $this->taskDependencyRepository->allForTask($task);
        if(!$dependencies) {
            throw new TaskDependencyNotFound;
        }
        return $dependencies;
    }

    public function addDependency($taskId, $dependsOnTaskId)
    {
        if ($taskId == $dependsOnTaskId) {
            throw new \Exception('A task cannot depend on itself.');
        }

        $task = $this->taskRepository->find($taskId);
        $dependsOnTask = $this->taskRepository->find($dependsOnTaskId);
        if (!$task || !$dependsOnTask) {
            throw new TaskNotFoundException;
        }

        if ($this->taskDependencyRepository->dependencyExists($task, $dependsOnTaskId)) {
            throw new TaskDependencyExistException;
        }

        if ($this->hasCircularDependency($taskId, $dependsOnTaskId)) {
            throw new CircularDependencyException;
        }

        return $this->taskDependencyRepository->create($task, $dependsOnTaskId);
    }

    public function removeDependency($taskId, $dependsOnTaskId)
    {
        $task = $this->taskRepository->find($taskId);
        $dependsOnTask = $this->taskRepository->find($dependsOnTaskId);
        if (!$task || !$dependsOnTask) {
            throw new TaskNotFoundException;
        }
        return $this->taskDependencyRepository->delete($task, $dependsOnTaskId);
    }

    protected function hasCircularDependency($taskId, $dependsOnTaskId)
    {
        // Check if the task we want to depend on already depends on us (directly or indirectly)
        return $this->taskDependsOn($dependsOnTaskId, $taskId);
    }

    protected function taskDependsOn($taskId, $targetTaskId, $visited = [])
    {
        if (in_array($taskId, $visited)) {
            return false;
        }

        $visited[] = $taskId;

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return false;
        }

        foreach ($task->dependencies as $dependency) {
            if ($dependency->id == $targetTaskId) {
                return true;
            }

            if ($this->taskDependsOn($dependency->id, $targetTaskId, $visited)) {
                return true;
            }
        }

        return false;
    }
}
