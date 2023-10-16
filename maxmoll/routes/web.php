<?php

use App\Http\Controllers\MovementHistoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/warehouses', function () {
    return view('warehouses', ['warehouses' => Warehouse::all()]);
});

Route::get('/products', [ProductController::class, 'index']);
Route::resource('orders', OrderController::class);
Route::post('/order/{id}/complete', [OrderController::class, 'complete']);
Route::post('/order/{id}/cancel', [OrderController::class, 'cancel']);
Route::post('/order/{id}/resume', [OrderController::class, 'resume']);

Route::get('/histories', [MovementHistoryController::class, 'index'])->name('history.index');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
