<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskImageService
{
    public function CreateImage(Request $request, Task $task)
    {
        if ($request->hasFile('image')) {
            $images = $request->file('image');
            $filename = 'todo_' . time() . '.' . $images->getClientOriginalExtension();
            $path = public_path('images/tasks/' . $filename);
            $images->move(public_path('images/tasks'), $filename);
            $task->images = $filename;
            $task->save();
        }
    }

    public function deleteImage(Task $task)
    {
        if ($task->images) {
            $image_path = public_path('images/tasks/' . $task->images);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $task->images = null;
            $task->save();
        }
    }
}
