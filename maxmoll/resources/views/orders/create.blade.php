<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orders — Create</title>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col">
            <h1>Создать заказ</h1>
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
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="customer" class="form-label">Имя клиента</label>
                    <input type="text" class="form-control" id="customer" name="customer">
                </div>

                <ul class="list-group">
                @foreach($products as $product)
                    <li class="list-group-item">
                        <div class="row">
                            <label for="product{{ $product->id }}" class="col-sm-2 col-form-label">{{ $product->name }}</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="product{{ $product->id }}" name="products[{{ $product->id }}]">
                            </div>
                        </div>
                    </li>
                @endforeach
                </ul>
                <button type="submit" class="btn btn-primary mt-4">Создать ордер</button>
            </form>
        </div>
    </div>
</div>
@vite(['resources/js/app.js'])
</body>
</html>
