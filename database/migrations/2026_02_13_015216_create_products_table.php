<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // nanti dari Digiflazz
            $table->string('buyer_sku_code')->unique();
            $table->string('product_name');
            $table->string('category')->index(); // Pulsa, Data, PLN, dll
            $table->string('brand')->index();    // Telkomsel, XL, Indosat
            $table->string('type')->nullable()->index(); // prepaid/postpaid dll

            $table->integer('price'); // harga jual (dummy dulu)
            $table->boolean('is_active')->default(true);

            // aturan input sederhana per kategori
            $table->enum('target_type', ['phone', 'customer'])->default('phone');
            // phone = nomor hp, customer = id pelanggan (PLN, PDAM, dll)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
