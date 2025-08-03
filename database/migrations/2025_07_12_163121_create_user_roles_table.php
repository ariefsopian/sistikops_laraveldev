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
        Schema::create('user_roles', function (Blueprint $table) {
            // Kolom user_id sebagai unsignedInteger, sesuai db_ticketing.sql
            $table->unsignedInteger('user_id');
            // Kolom role_id sebagai unsignedInteger, sesuai db_ticketing.sql
            $table->unsignedInteger('role_id');

            // Menetapkan primary key gabungan, sesuai db_ticketing.sql
            $table->primary(['user_id', 'role_id']);

            // Foreign Key ke tabel 'users' (kolom id)
            // onDelete('cascade') berarti jika user dihapus, relasi di user_roles juga dihapus
            // onUpdate('cascade') berarti jika user.id berubah, relasi di user_roles juga diupdate
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            // Foreign Key ke tabel 'roles' (kolom id)
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Mengembalikan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};