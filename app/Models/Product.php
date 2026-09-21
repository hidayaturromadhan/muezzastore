<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'buyer_sku_code',
        'product_name',
        'image',
        'category',
        'brand',
        'type',
        'price',
        'digiflazz_price',
        'digiflazz_status',
        'seller_name',
        'is_active',
        'target_type',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
