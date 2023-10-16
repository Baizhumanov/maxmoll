<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Histories</title>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col">
            <h1>История</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form action="{{ route('history.index') }}" method="GET" class="row g-3 mb-5 align-items-center">
                <div class="col-md-3">
                    <label for="product_name" class="form-label">Товар</label>
                    <input type="text" class="form-control" id="product_name" name="product_name">
                </div>
                <div class="col-md-3">
                    <label for="warehouse_name" class="form-label">Склад</label>
                    <input type="text" class="form-control" id="warehouse_name" name="warehouse_name">
                </div>
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Дата начала</label>
                    <input type="date" class="form-control" id="start_date" name="start_date">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Дата окончания</label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Применить фильтры</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Продукт</th>
                    <th scope="col">Склад</th>
                    <th scope="col">Старое значение</th>
                    <th scope="col">Новое значение</th>
                    <th scope="col">Дата и время</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($histories as $history)
                    <tr>
                        <th scope="row">{{ $history->id }}</th>
                        <td>{{ $history->product->name }}</td>
                        <td>{{ $history->warehouse->name }}</td>
                        <td>{{ $history->old_count }}</td>
                        <td>{{ $history->new_count }}</td>
                        <td>{{ $history->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    @if ($histories->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">Предыдущая</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $histories->previousPageUrl() }}" rel="prev">Предыдущая</a>
                        </li>
                    @endif

                    @for ($i = 1; $i <= $histories->lastPage(); $i++)
                        <li class="page-item{{ $histories->currentPage() == $i ? ' active' : '' }}">
                            <a class="page-link" href="{{ $histories->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($histories->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $histories->nextPageUrl() }}" rel="next">Следующая</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Следующая</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
@vite(['resources/js/app.js'])
</body>
</html>
