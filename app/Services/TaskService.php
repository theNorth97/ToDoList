<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public  function reorderByUser($userId)
    {
        $tasks = Task::where('user_id', $userId)->orderBy('created_at')->get();
        foreach ($tasks as $key => $task) {
            $task->order = $key + 1;
            $task->save();
        }
    }
    public function deleteTask(Task $task)
    {
        $userId = $task->user_id;
        $task->delete();
        $this->reorderByUser($userId);
    }

}

