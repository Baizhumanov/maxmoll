<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orders</title>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col">
            <h1>Заказы</h1>
        </div>
        <div class="col text-end">
            <a href="{{ url('orders/create') }}" class="btn btn-primary">Создать заказ</a>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form action="{{ route('orders.index') }}" method="GET" class="row g-3 mb-5 align-items-center">
                <div class="col-md-3">
                    <label for="customer" class="form-label">Имя клиента</label>
                    <input type="text" class="form-control" id="customer" name="customer">
                </div>
                <div class="col-md-3">
                    <select class="form-select" aria-label="Default select example" name="status">
                        <option value="" selected>Выберите статус</option>
                        <option value="active">Активный</option>
                        <option value="completed">Завершен</option>
                        <option value="canceled">Отменен</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Дата начала создания</label>
                    <input type="date" class="form-control" id="start_date" name="start_date">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Дата окончания создания</label>
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
                    <th scope="col">Имя клиента</th>
                    <th scope="col">Ордер создан</th>
                    <th scope="col">Ордер выполнен</th>
                    <th scope="col">Статус</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <th scope="row">{{ $order->id }}</th>
                        <td>{{ $order->customer }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->completed_at }}</td>
                        <td>{{ $order->status }}</td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">Изменить</a>
                                <form method="POST" action="/order/{{ $order->id }}/complete">
                                    @csrf
                                    <button type="submit" class="btn btn-primary ms-2">Завершить заказ</button>
                                </form>
                                <form method="POST" action="/order/{{ $order->id }}/cancel">
                                    @csrf
                                    <button type="submit" class="btn btn-primary ms-2">Отменить заказ</button>
                                </form>
                                <form method="POST" action="/order/{{ $order->id }}/resume">
                                    @csrf
                                    <button type="submit" class="btn btn-primary ms-2">Возобновить заказ</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    @if ($orders->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">Предыдущая</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">Предыдущая</a>
                        </li>
                    @endif

                    @for ($i = 1; $i <= $orders->lastPage(); $i++)
                        <li class="page-item{{ $orders->currentPage() == $i ? ' active' : '' }}">
                            <a class="page-link" href="{{ $orders->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($orders->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">Следующая</a>
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
