<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Вывести список продуктов с их остатками по складам
     */
    public function index(): View
    {
        return view('products', [
            'products' => Product::all()
        ]);
    }
}
