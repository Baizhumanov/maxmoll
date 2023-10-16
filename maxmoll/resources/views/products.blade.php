<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col">
            <h1>Товары</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @foreach($products as $product)
                <div class="card m-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Общее кол-во: {{ $product->stock }}</h6>
                        <ul class="list-group list-group-flush">
                            @forelse ($product->stocks as $stock)
                                <li class="list-group-item">Склад {{ $stock->warehouse->name }} ({{ $stock->stock }})</li>
                            @empty
                                <p>Продукты отсутствуют в каком-либо складе</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@vite(['resources/js/app.js'])
</body>
</html>
