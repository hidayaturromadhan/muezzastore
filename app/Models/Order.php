<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'target',
        'gross_amount',
        'status',
        'midtrans_order_id',
        'midtrans_token',
        'midtrans_redirect_url',
        'midtrans_payload',
        'digiflazz_trx_id',
        'digiflazz_rc',
        'digiflazz_message',
        'digiflazz_response',
        'digiflazz_sn',
    ];

    protected $casts = [
        'midtrans_payload' => 'array',
        'digiflazz_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
