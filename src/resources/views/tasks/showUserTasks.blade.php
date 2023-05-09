@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Список задач</h1>

        <div class="filter-section text-center">
            <div class="input-group mb-3 justify-content-center">
                <label for="search"></label>
                <input type="text" class="form-control" placeholder="Поиск" id="search">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="filter-btn">Поиск</button>
                </div>
            </div>
            <a href="{{ route('tasks.showUserTasks') }}">Сбросить поиск</a>
            <p id="search-no-results" class="my-3" style="display: none;"></p>
        </div>

        <form method="GET" action="{{ route('tasks.filter') }}" class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="title" class="form-label">Наименование:</label>
                <input type="text" class="form-control" name="title" id="title">
            </div>
            <div class="col-auto">
                <label for="description" class="form-label">Описание:</label>
                <input type="text" class="form-control" name="description" id="description">
            </div>
            <div class="col-auto">
                <label for="status" class="form-label">Статус:</label>
                <select class="form-select" name="status" id="status">
                    <option value="В процессе">В процессе</option>
                    <option value="Выполнена">Выполнена</option>
                </select>
            </div>
            <div class="col-auto">
                <label for="tag" class="form-label">Тег:</label>
                <input type="text" class="form-control" name="tag" id="tag">
            </div>

            <div class="col-auto mx-auto">
                <button type="submit" class="btn btn-primary mb-3 filter-button">Фильтровать</button>
                <a href="{{ route('tasks.showUserTasks') }}">Сбросить фильтр</a>
            </div>

        </form>

        <style>

            .filter-button {
                margin-top: 14px;
            }

            table td, table th {
                padding-top: 0;
                padding-bottom: 0;
                vertical-align: middle;
            }


            h1 {
                text-align: center;
            }

            .task-image {
                object-fit: cover;
                width: 130px;
                height: 130px;
            }

            .filter-section {
                margin-bottom: 20px;
                margin-top: 20px;
                text-align: center;
            }

            #search-no-results {
                text-align: center;
            }

            .filter-section {
                margin-bottom: 20px;
            }

            #search {
                max-width: 400px;
            }

            #filter-btn {
                margin-left: 10px;
            }

        </style>

    @if ($tasks->count())
            <table class="table">
                <thead>
                <tr>
                    <th style="width:10%; text-align: center;" >Изображение</th>
                    <th style="vertical-align: middle;">Тег</th>
                    <th style="vertical-align: middle;">Название</th>
                    <th style="vertical-align: middle;">Описание</th>
                    <th style="vertical-align: middle;">Статус</th>
                    <th colspan="3" style="text-align: center;">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tasks as $task)
                    <tr>

                        <!-- Столбец для превью изображения -->
                        <td>
                            @if ($task->image)
                                <a href="{{ asset('storage/images/' . $task->image) }}" target="_blank">
                                    <img src="{{ asset('storage/images/' . $task->image) }}" alt="{{ $task->title }}" class="task-image">
                                </a>
                            @else
                                <img src="{{ asset('storage/images/no_image.jpg') }}" alt="Default Image" class="task-image">
                            @endif
                        </td>

                        <td>{{ $task->tags->isNotEmpty() ? $task->tags->first()->name : '-' }}</td>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->description }}</td>
                        <td>{{ $task->status }}</td>

                        <td style="text-align: center; vertical-align: middle;">
                            <!-- кнопка вызова модального окна для просмотра задачи -->
                            <button type="button" class="btn btn-info btn-view-task mr-2" data-toggle="modal" data-target="#exampleModal{{ $task->id }}">Просмотр</button>
                        </td>
                        @include('tasks.modals.showTaskModal', ['task' => $task])

                        <td style="text-align: center; vertical-align: middle;">
                            <!-- кнопка вызова модального окна для изменения задачи -->
                            <button type="button" class="btn btn-info btn-edit-task" data-toggle="modal" data-target="#editTaskModal{{ $task->id }}">Изменить</button>
                        </td>
                        @include('tasks.modals.editTaskModal', ['task' => $task])

                        <!-- кнопка для удаления задачи -->
                        <td style="text-align: center; vertical-align: middle;">
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger " style="display: block;">Удалить</button>
                            </form>
                        </td>

                @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center my-5">
                <p>Задачи отсутствуют</p>
            </div>
        @endif

        <div class="text-center my-5">
            <button class="btn btn-primary btn-create-task">Создать задачу</button>
        </div>
        <!-- кнопка  вызова модального окна для создания задачи -->
            <form id="createTaskFormModal"  method="POST" action="{{ route('tasks.store') }}">
                {{ csrf_field() }}
                @include('tasks.modals.createTaskModal')



{{--                <div class="container">--}}
{{--                    <form action="{{ route('image.upload') }}" method="post" enctype="multipart/form-data">--}}
{{--                        {{ csrf_field() }}--}}

{{--                        <div class="form-group">--}}
{{--                            <input type="file" name="image">--}}
{{--                        </div>--}}
{{--                        <button class="btn btn-default" type="submit">загрузка!</button>--}}
{{--                    </form>--}}

{{--                    @isset($path)--}}
{{--                        <img class="img-fluid" src="{{ asset('/storage/' . $path) }}" alt="">--}}
{{--                    @endisset--}}
{{--                </div>--}}

@endsection



