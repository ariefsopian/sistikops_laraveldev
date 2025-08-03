<?php // Pastikan tidak ada spasi atau baris kosong di atas baris ini

namespace Database\Seeders; // Ini harus menjadi pernyataan pertama setelah <?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     *
     * @return void
     */
    public function run(): void
    {
        // Panggil seeder dalam urutan yang benar berdasarkan ketergantungan tabel:
        // 1. RoleSeeder: Tabel 'roles' harus ada terlebih dahulu.
        $this->call(RoleSeeder::class);

        // 2. UserSeeder: Tabel 'users' harus ada, dan 'roles' harus sudah terisi
        //    karena UserSeeder akan melampirkan peran kepada pengguna.
        $this->call(UserSeeder::class);

        // 3. TicketSeeder: Tabel 'tickets' bergantung pada 'users' (requester_id, assignee_id).
        $this->call(TicketSeeder::class);

        // 4. AttachmentSeeder: Tabel 'attachments' bergantung pada 'tickets' (ticket_id).
        $this->call(AttachmentSeeder::class);

        // Jika Anda memiliki seeder lain di masa mendatang, tambahkan di sini
        // dengan mempertimbangkan urutan ketergantungan mereka.
    }
}
