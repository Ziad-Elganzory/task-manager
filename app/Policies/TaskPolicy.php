<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Managers can view all tasks, users can view their assigned tasks (handled in controller/scopes)
        return $user->hasRole('manager') || $user->hasRole('user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Managers can view any task, users only their assigned tasks
        return $user->hasRole('manager') || $user->id === $task->assigned_to;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only managers can create tasks
        return $user->hasRole('manager');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Managers can update any task
        if ($user->hasRole('manager')) {
            return true;
        }
        // Users can only update the status of their assigned tasks
        return $user->id === $task->assigned_to;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Only managers can delete tasks
        return $user->hasRole('manager');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        // Only managers can restore tasks
        return $user->hasRole('manager');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // Only managers can force delete tasks
        return $user->hasRole('manager');
    }
}
