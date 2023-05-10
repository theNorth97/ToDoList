<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'min:1', 'max:35', 'required_with:tags'],
            'tags' => ['required_without_all:tag1,tag2,tag3', 'min:1', 'max:35'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'title.required' => 'Поле "Название" не должно быть пустым',
            'tags.required' => 'Поле "Тег" не должно быть пустым',
            'status.required' => 'Пожалуйста, выберите статус задачи',
        ]);

        $task = new Task();
        $task->title = $validated['title'];
        $task->user_id = auth()->id();
        $task->status = $request->status;

        $task->save();

        $this->storeTags($request, $task);

        return redirect()->route('tasks.showUserTasks');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['required', 'min:3','max:35'],
            'description' =>['required', 'min:3','max:35'],
            'status' => 'required|in:В процессе,Выполнена,Выбирите статус',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $task->title = $validated['title'];
        $task->description = $request->input('description');
        $task->status = $request->status;

        if ($request->hasFile('image')) {
            $this->updateImage($request, $task);
        } elseif ($request->input('delete_image')) {
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

    public function destroy(Task $task)
    {
        $task->tags()->detach(); // сначала удаляю все связи с тегами, чтобы они не оставались в БД после удаления задачи.
        $task->delete();

        return redirect()->route('tasks.showUserTasks');
    }

    public function showUserTasks()
    {
        $tasks = auth()->user()->tasks;
        return view('tasks.showUserTasks', compact('tasks'));
    }

    public function storeTags(Request $request, Task $task)
    {
        $tags = array_map('trim', explode(',', $request->input('tags')));
        $task->tags()->detach();
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $task->tags()->attach($tag);
        }
        return redirect()->route('tasks.showUserTasks');
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
