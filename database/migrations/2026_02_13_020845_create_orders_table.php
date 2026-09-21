<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            $table->string('target'); // nomor hp / customer id
            $table->integer('gross_amount');

            // status internal
            $table->string('status')->default('pending_payment');
            // pending_payment | paid | failed | fulfilled

            // midtrans
            $table->string('midtrans_order_id')->unique();
            $table->string('midtrans_token')->nullable();
            $table->text('midtrans_redirect_url')->nullable();
            $table->json('midtrans_payload')->nullable();

            $table->timestamps();

            $table->index(['user_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
