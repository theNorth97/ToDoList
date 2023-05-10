<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'min:3', 'max:35'],
            'description' => ['required', 'min:3', 'max:255'],
            'status' => 'required|in:В процессе,Выполнена',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => ['required_without_all:tag1,tag2,tag3', 'min:1', 'max:35'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Поле "Название" не должно быть пустым',
            'title.min' => 'Название должно быть не менее :min символов',
            'title.max' => 'Название должно быть не более :max символов',
            'description.required' => 'Поле "Описание" не должно быть пустым',
            'description.min' => 'Описание должно быть не менее :min символов',
            'description.max' => 'Описание должно быть не более :max символов',
            'status.required' => 'Пожалуйста, выберите статус задачи',
            'status.in' => 'Поле "Статус" имеет неверное значение',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Файл должен быть в формате jpeg, png, jpg или gif',
            'image.max' => 'Размер файла не должен превышать :max Кб',
            'tags.max' => 'Максимальная длина тега :max символов',
            'tags.required' => 'Поле "Тег" не должно быть пустым',
        ];
    }
}
