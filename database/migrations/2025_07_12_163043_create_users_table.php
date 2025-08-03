<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Kolom 'id' sebagai Primary Key dan auto-increment, sesuai db_ticketing.sql
            $table->increments('id');
            // Kolom 'username' sebagai string unik dengan panjang 100, sesuai db_ticketing.sql
            $table->string('username', 100)->unique();
            // Kolom 'password'. Laravel meng-hash password, jadi tidak perlu batasan panjang spesifik
            $table->string('password');
            // Kolom untuk fitur "Remember Me" (opsional, tapi bagus untuk autentikasi Laravel)
            $table->rememberToken();
            // Menambahkan kolom 'created_at' dan 'updated_at' secara otomatis
            $table->timestamps();
        });
    }

    /**
     * Mengembalikan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};