<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskDependencyRequest;
use App\Services\TaskService;
use App\Services\TaskDependencyService;
use Illuminate\Http\Request;

class TaskDependencyController extends Controller
{
    public function __construct(
        protected TaskService $taskService,
        protected TaskDependencyService $taskDependencyService
    ) {}

    /**
     * List all dependencies for a task.
     */
    public function index($taskId)
    {
        try{
            $task = $this->taskService->getTask($taskId);
            $this->authorize('view', $task);
            $dependencies = $this->taskDependencyService->listDependencies($taskId);
            return response()->json(['dependencies' => $dependencies]);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            if ($e instanceof \App\Exceptions\TaskDependencyNotFound) {
                return response()->json(['error' => 'No dependencies found for this task.'], 404);
            }
            return response()->json(['error' => 'Failed to retrieve dependencies.'], 500);
        }
    }

    /**
     * Add a dependency to a task.
     */
    public function store(CreateTaskDependencyRequest $request, $taskId)
    {
        try {
            $task = $this->taskService->getTask($taskId);
            $this->authorize('update', $task);
            $validated = $request->validated();
            $this->taskDependencyService->addDependency($taskId, $validated['depends_on_task_id']);
            return response()->json(['message' => 'Dependency added successfully.']);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            if ($e instanceof \App\Exceptions\CircularDependencyException) {
                return response()->json(['error' => 'Circular dependency detected.'], 422);
            }
            if ($e instanceof \App\Exceptions\TaskDependencyExistException) {
                return response()->json(['error' => 'Dependency already exists.'], 409);
            }
            return response()->json(['error' => 'Failed to add dependency.'], 500);
        }

    }

    /**
     * Remove a dependency from a task.
     */
    public function destroy($taskId, $dependsOnTaskId)
    {
        try{
            $task = $this->taskService->getTask($taskId);
            $this->authorize('update', $task);
            $this->taskDependencyService->removeDependency($taskId, $dependsOnTaskId);
            return response()->json(['message' => 'Dependency removed successfully.']);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            return response()->json(['error' => 'Failed to remove dependency.'], 500);
        }
    }
}
