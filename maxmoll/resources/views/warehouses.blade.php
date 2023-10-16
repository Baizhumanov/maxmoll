<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Warehouses</title>
</head>
<body>
    <div class="container">
        <div class="row mt-5">
            <div class="col">
                <h1>Склады</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @foreach($warehouses as $warehouse)
                    <div class="card m-3">
                        <div class="card-body">
                            {{ $warehouse->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @vite(['resources/js/app.js'])
</body>
</html>
