<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskTagService
{
    public function storeTags(Request $request, Task $task)
    {
        $tags = array_map('trim', explode(',', $request->input('tags')));
        $task->tags = implode(',', $tags);
        $task->save();
    }
}
