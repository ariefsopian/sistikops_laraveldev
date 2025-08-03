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
        Schema::create('attachments', function (Blueprint $table) {
            // Kolom 'id' sebagai Primary Key dan auto-increment, sesuai db_ticketing.sql
            $table->increments('id');
            // Kolom 'ticket_id' (unsignedInteger karena FK), sesuai db_ticketing.sql
            $table->unsignedInteger('ticket_id');
            // Kolom 'file_name' sebagai string dengan panjang 255, sesuai db_ticketing.sql
            $table->string('file_name', 255);
            // Menambahkan kolom 'created_at' dan 'updated_at' secara otomatis, sesuai db_ticketing.sql
            $table->timestamps(); // db_ticketing.sql hanya punya created_at, tapi Laravel's timestamps() lebih praktis

            // Foreign Key ke tabel 'tickets' (kolom id)
            // onDelete('cascade') berarti jika tiket dihapus, lampiran terkait juga dihapus
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Mengembalikan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};