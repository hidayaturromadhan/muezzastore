<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('username', 50)->unique();
            $table->string('email', 255)->unique();
            $table->string('password');

            // role user
            $table->string('role', 20)->default('buyer'); // buyer | admin

            $table->rememberToken();
            $table->timestamps();

            $table->index('username');
            $table->index('email');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
