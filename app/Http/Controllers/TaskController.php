<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\TaskTagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TaskImageService;
use App\Services\TaskService;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param $taskValidationService
     */

    public function store(TaskRequest $request)
    {
        $task = new Task();
        $task->title = $request->validated()['title'];
        $task->description = $request->validated()['description'];
        $task->user_id = auth()->id();
        $task->status = $request->validated()['status'];
        $task->order = Task::where('user_id', auth()->id())->max('order') + 1; //сохраняю следующий порядковый номер
        $task->save();

        $taskTagService = new TaskTagService();
        $taskTagService->storeTags($request, $task);

        if ($request->hasFile('image')) {
            $taskImageService = new TaskImageService();
            $taskImageService->CreateImage($request, $task);
        }

        return redirect()->route('tasks.showUserTasks');

    }

    public function update(TaskRequest $request, Task $task)
    {
        $task->title = $request->validated()['title'];
        $task->status = $request->validated()['status'];
        $task->description = $request->validated()['description'];
        $task->save();

        $taskImageService = new TaskImageService();
        $taskImageService->CreateImage($request, $task);

        $taskTagService = new TaskTagService();
        $taskTagService->storeTags($request, $task);

        if ($request->has('delete_image')) {
            $taskImageService->deleteImage($task);
        }

        return redirect()->route('tasks.showUserTasks');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function destroy(Task $task)
    {
        $userId = auth()->id();
        if ($task->user_id === $userId) {
            $taskService = new TaskService();
            $taskService->deleteTask($task);
        }
        return redirect()->route('tasks.showUserTasks');
    }

    public function showUserTasks()
    {
        $tasks = auth()->user()->tasks()->paginate(5);
        return view('tasks.showUserTasks', compact('tasks'));
    }

    public function filter(Request $request)
    {
        $title = $request->input('title');
        $description = $request->input('description');
        $status = $request->input('status');
        $tag = $request->input('tag');

        $tasks = Auth::user()->tasks()
            ->when($title, function($query, $title) {
                return $query->where('title', 'like', '%'.$title.'%');
            })
            ->when($description, function($query, $description) {
                return $query->where('description', 'like', '%'.$description.'%');
            })
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($tag, function($query, $tag) {
                return $query->where('tags', 'like', '%'.$tag.'%');
            })
            ->paginate();

        return view('tasks.showUserTasks', compact('tasks'));
    }

}
