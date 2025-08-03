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
        Schema::create('tickets', function (Blueprint $table) {
            // Kolom 'id' sebagai Primary Key dan auto-increment, sesuai db_ticketing.sql
            $table->increments('id');
            // Kolom 'ticket_no' sebagai string unik dengan panjang 20, sesuai db_ticketing.sql
            $table->string('ticket_no', 20)->unique();
            // Kolom 'requester_id' (unsignedInteger karena FK), sesuai db_ticketing.sql
            $table->unsignedInteger('requester_id');
            // Kolom 'type' sebagai string dengan panjang 50, sesuai db_ticketing.sql
            $table->string('type', 50);
            // Kolom 'subject' sebagai text, sesuai db_ticketing.sql
            $table->text('subject');
            // Kolom 'description' sebagai text, bisa nullable, sesuai db_ticketing.sql
            $table->text('description')->nullable();
            // Kolom 'due_date' sebagai tanggal, sesuai db_ticketing.sql
            $table->date('due_date');
            // Kolom 'priority' sebagai enum, sesuai db_ticketing.sql
            $table->enum('priority', ['Low', 'Medium', 'High']);
            // Kolom 'status' sebagai enum, sesuai db_ticketing.sql
            $table->enum('status', ['Open', 'In Progress', 'Pending Approval', 'Resolved', 'Closed']);
            // Kolom 'assignee_id' (unsignedInteger, bisa nullable), sesuai db_ticketing.sql
            $table->unsignedInteger('assignee_id')->nullable();
            // Menambahkan kolom 'created_at' dan 'updated_at' secara otomatis, sesuai db_ticketing.sql
            $table->timestamps();

            // Foreign Key ke tabel 'users' (untuk requester_id)
            $table->foreign('requester_id')->references('id')->on('users')->onUpdate('cascade');
            // Foreign Key ke tabel 'users' (untuk assignee_id)
            // onDelete('set null') berarti jika assignee dihapus, assignee_id di tiket menjadi NULL
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Mengembalikan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};