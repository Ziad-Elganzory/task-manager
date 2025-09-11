<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Models\Task;

class TaskController extends Controller
{

    public function __construct(protected TaskService $taskService){}
    /**
     * Display a listing of the tasks (with filters).
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Task::class);
            $user = $request->user();
            $filters = $request->only(['status', 'due_date_from', 'due_date_to', 'assigned_to']);

            if($user->hasRole('manager')){
                $tasks = $this->taskService->getAllTasks($filters);
            } else if($user->hasRole('user')){
                $filters['assigned_to'] = $user->id;
                $tasks = $this->taskService->getAllTasks($filters);
            } else {
                // User has no valid role
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            return response()->json([
                'message' => 'Tasks found.',
                'data' => $tasks,
            ]);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'No tasks found.'], 404);
            }
            return response()->json(['error' => 'Failed to retrieve tasks.'], 500);
        }
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(CreateTaskRequest $request)
    {
        try {
            $this->authorize('create', Task::class);
            $validated = $request->validated();
            $validated['created_by'] = $request->user()->id;
            $task = $this->taskService->createTask($validated);
            return response()->json($task, 201);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskCreationFailedException) {
                return response()->json(['error' => 'Failed to create task.'], 500);
            }
            return response()->json(['error' => 'Failed to create task.'], 500);
        }
    }

    /**
     * Display the specified task with dependencies.
     */
    public function show($id)
    {
        try {
            $task = $this->taskService->getTask($id);
            $this->authorize('view', $task);
            $task->load(['dependencies', 'dependents', 'assignee', 'creator']);
            return response()->json($task);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            return response()->json(['error' => 'Failed to retrieve task.'], 500);
        }
    }

    /**
     * Update the specified task in storage (details or status).
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = $this->taskService->getTask($id);
            $this->authorize('update', $task);
            $validated = $request->validated();
            $updatedTask = $this->taskService->updateTask($task, $validated);
            return response()->json($updatedTask);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            if ($e instanceof \App\Exceptions\CompleteDependencyException) {
                return response()->json(['error' => 'Cannot complete task with incomplete dependencies.'], 422);
            }
            return response()->json(['error' => 'Failed to update task.'], 500);
        }
    }

    /**
     * Remove the specified task from storage.
     */
    /**
     * Soft delete the specified task.
     */
    public function destroy($id)
    {
        try {
            $task = $this->taskService->getTask($id);
            $this->authorize('delete', $task);
            $this->taskService->softDeleteTask($task);
            return response()->json(['message' => 'Task soft deleted successfully.']);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            return response()->json(['error' => 'Failed to delete task.'], 500);
        }
    }

    /**
     * Permanently delete the specified task.
     */
    public function forceDestroy($id)
    {
        try {
            $task = $this->taskService->getTaskWithTrashed($id);
            $this->authorize('forceDelete', $task);
            $this->taskService->forceDeleteTask($task);
            return response()->json(['message' => 'Task permanently deleted.']);
        } catch (\Exception $e) {
            // Check if it's a specific exception type
            if ($e instanceof \App\Exceptions\TaskNotFoundException) {
                return response()->json(['error' => 'Task not found.'], 404);
            }
            return response()->json(['error' => 'Failed to permanently delete task.'], 500);
        }
    }
}
