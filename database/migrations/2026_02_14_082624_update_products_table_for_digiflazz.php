<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // harga dari Digiflazz (modal)
            $table->integer('digiflazz_price')->nullable()->after('price');

            // status/availability dari Digiflazz
            $table->string('digiflazz_status')->nullable()->after('digiflazz_price');

            // untuk filter produk
            $table->string('seller_name')->nullable()->after('digiflazz_status'); // kadang ada di price list
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['digiflazz_price','digiflazz_status','seller_name']);
        });
    }
};
