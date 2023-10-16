<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                "name" => "Always",
                "price" => "2000",
                "stock" => "10"
            ],
            [
                "name" => "Snickers",
                "price" => "500",
                "stock" => "15"
            ],
            [
                "name" => "Coca-Cola",
                "price" => "500",
                "stock" => "8"
            ],
            [
                "name" => "Tide",
                "price" => "4500",
                "stock" => "3"
            ],
            [
                "name" => "Creatine",
                "price" => "5000",
                "stock" => "2"
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
