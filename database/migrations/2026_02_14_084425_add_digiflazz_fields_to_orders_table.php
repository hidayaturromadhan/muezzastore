<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('digiflazz_trx_id')->nullable()->after('midtrans_payload');
            $table->json('digiflazz_response')->nullable()->after('digiflazz_trx_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'digiflazz_trx_id',
                'digiflazz_response'
            ]);
        });
    }
};
