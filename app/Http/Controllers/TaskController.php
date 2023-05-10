<?php

namespace App\Http\Controllers;

use App\Http\Services\TaskValidationService;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param $taskValidationService
     */

    public function store(Request $request, TaskValidationService $taskValidationService)
    {
        $validated = $taskValidationService->validateTask($request);

        $task = new Task();
        $task->title = $validated['title'];
        $task->user_id = auth()->id();
        $task->status = $request->input('status');

        $task->save();

        $this->storeTags($request, $task);

        return redirect()->route('tasks.showUserTasks');
    }

    public function update(Request $request, Task $task, TaskValidationService $taskValidationService)
    {
        $validated = $taskValidationService->validateTask($request);

        $task->title = $validated['title'];
        $task->description = $request->input('description');
        $task->status = $request->input('status');

        if ($request->hasFile('image')) {
            $this->updateImage($request, $task);
        }

        if ($request->has('delete_image')) {
            Storage::delete('public/images/' . $task->image);
            $task->image = null;
        }

        $this->storeTags($request, $task);

        $task->save();

        return redirect()->route('tasks.showUserTasks');
    }

    protected function updateImage(Request $request, Task $task)
    {
        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('public/images', $filename);
        $task->image = $filename;
    }

    private function storeTags(Request $request, Task $task)
    {
        $tags = array_map('trim', explode(',', $request->input('tags')));
        $task->tags()->detach();
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $task->tags()->attach($tag);
        }
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function destroy(Task $task)
    {
        $task->tags()->detach();
        $task->delete();

        return redirect()->route('tasks.showUserTasks');
    }

    public function showUserTasks()
    {
        $tasks = auth()->user()->tasks;
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
                return $query->whereHas('tags', function($query) use ($tag) {
                    $query->where('name', $tag);
                });
            })
            ->get();

        return view('tasks.showUserTasks', compact('tasks'));
    }

}
