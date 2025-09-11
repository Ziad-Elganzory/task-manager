<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function __construct(protected Task $taskModel){}
    public function all($filters = [])
    {
        $query = $this->taskModel->query();
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['due_date_from'])) {
            $query->where('due_date', '>=', $filters['due_date_from']);
        }
        if (!empty($filters['due_date_to'])) {
            $query->where('due_date', '<=', $filters['due_date_to']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        return $query->get();
    }

    public function find($id)
    {
        return $this->taskModel->find($id); // Returns null if not found
    }

    public function create(array $data)
    {
        return $this->taskModel->create($data);
    }

    public function update(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }
    public function softDelete(Task $task)
    {
        return $task->delete();
    }

    public function forceDelete(Task $task)
    {
        return $task->forceDelete();
    }

    public function findWithTrashed($id)
    {
        return $this->taskModel->withTrashed()->find($id); // Returns null if not found
    }
}
