<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::truncate();

        Product::insert([
            [
                'buyer_sku_code' => 'TSEL10',
                'product_name' => 'Pulsa Telkomsel 10.000',
                'category' => 'Pulsa',
                'brand' => 'Telkomsel',
                'type' => 'prepaid',
                'price' => 10,
                'is_active' => true,
                'target_type' => 'phone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_sku_code' => 'TSEL25',
                'product_name' => 'Pulsa Telkomsel 25.000',
                'category' => 'Pulsa',
                'brand' => 'Telkomsel',
                'type' => 'prepaid',
                'price' => 20000,
                'is_active' => true,
                'target_type' => 'phone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_sku_code' => 'PLN20',
                'product_name' => 'PLN Token 20.000',
                'category' => 'PLN',
                'brand' => 'PLN',
                'type' => 'prepaid',
                'price' => 23000,
                'is_active' => true,
                'target_type' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
