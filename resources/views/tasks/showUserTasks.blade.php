@extends('layouts.app')
@section('content')
                                            <!-- Создание задачи -->
    <button type="button" class="btn btn-primary rounded-circle" data-toggle="modal" data-target="#createTaskModal">
        <i class="fas fa-plus"></i>
    </button>

    <div class="container" id="tasklist">
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
                    <tr class="task-row">

                        <!-- Столбец для превью изображения -->
                        <td>
                            @if ($task->images)
                                <a href="{{ asset('images/tasks/' . $task->images) }}" target="_blank">
                                    <img src="{{ asset('images/tasks/' . $task->images) }}" alt="{{ $task->title }}" class="task-image">
                                </a>
                            @else
                                <img src="{{ asset('storage/images/no_image.jpg') }}" alt="Default Image" class="task-image">
                            @endif
                        </td>

                        <td>{{ $task->tags }}</td>
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

                    </tr>
                @endforeach
                </tbody>
            </table>
            <!-- Пагинация -->
            @if ($tasks->lastPage() > 1)
                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
                    <!-- Pagination Code  -->
                    <div class="pagination-container">
                        {{ $tasks->links() }}
                    </div>
                </nav>
            @endif

        @else
            <div class="text-center my-5">
                <p>Задачи отсутствуют</p>
            </div>
        @endif

        <!-- кнопка  вызова модального окна для создания задачи -->
        <form id="createTaskFormModal"  method="POST" action="{{ route('tasks.store') }}">
            {{ csrf_field() }}
            @include('tasks.modals.createTaskModal')

             <!-- подключаю js поиск -->
        <script src="{{ asset('js/search.js') }}"></script>
            <!-- подключаю css  -->
        <link href="{{ asset('css/styleShowUserTasks.css') }}" rel="stylesheet">
        <link href="{{ asset('css/stylePaginate.css') }}" rel="stylesheet">

@endsection




