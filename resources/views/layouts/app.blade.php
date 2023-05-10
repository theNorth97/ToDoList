<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'To-Do-List')</title>

    <!-- подключение библиотек -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
</head>

<style>
</style>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">

            @auth
                <li class="nav-item">
                    <span class="nav-link">{{ auth()->user()->name }}</span>
                </li>
                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">{{ __('Выйти из профиля') }}</button>
                    </form>
                </li>
            @endauth

            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endguest
        </ul>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>
</body>

<body style="background-color: #e5e5e5;">

<script>

    // Кнопка создать задачу
    $(document).on('click', '.btn-create-task', function() {
        $('#createTaskModal').modal('show');
    });

    <!-- поиск  -->
    $(document).ready(function() {
        $('#filter-btn').click(function() {
            var searchVal = $('#search').val().toLowerCase();
            if (searchVal === '') {
                $('.table tbody tr').show();
                $('#search-no-results').hide();
            } else {
                $('.table tbody tr').each(function(index, row) {
                    var allCells = $(row).find('td');
                    if(allCells.length > 0) {
                        var found = false;
                        allCells.each(function(index, td) {
                            var regExp = new RegExp(searchVal, 'i');
                            if(regExp.test($(td).text())) {
                                found = true;
                                return false;
                            }
                        });
                        if(found === true) {
                            $(row).show();
                        } else {
                            $(row).hide();
                        }
                    }
                });
                if($('.table tbody tr:visible').length === 0) {
                    $('#search-no-results').text('Такой задачи нет').show();
                } else {
                    $('#search-no-results').hide();
                }
            }
        });
    });

    <!-- предотвратить отправку формы на сервер  -->
    $(document).on('click', '#createTaskBtn', function(event) {
        event.preventDefault();
        var form = $('#createTaskFormModal');
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function() {
                $('#createTaskModal').modal('hide');
                location.reload();
            },
            error: function(response) {
                var errors = response.responseJSON.errors;
                if (errors) {
                    for (var key in errors) {
                        var input = form.find('input[name="' + key + '"], select[name="' + key + '"]');
                        input.addClass('is-invalid');
                        var feedback = input.siblings('.invalid-feedback');
                        if (errors[key][0] === "The " + key + " field is required.") {
                            feedback.text('Это поле обязательно для заполнения.');
                        } else {
                            feedback.text(errors[key][0]);
                        }
                    }
                }
            }
        });
    });

</script>

