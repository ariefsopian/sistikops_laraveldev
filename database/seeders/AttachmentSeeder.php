<?php // Pastikan tidak ada spasi atau baris kosong di atas baris ini

namespace Database\Seeders; // Ini harus menjadi pernyataan pertama setelah <?php

use Illuminate\Database\Seeder;
use App\Models\Attachment; // Pastikan baris ini ada untuk mengimpor model Attachment
use App\Models\Ticket;     // Pastikan baris ini ada untuk mengimpor model Ticket
use Carbon\Carbon;          // Pastikan baris ini ada untuk mengimpor Carbon (untuk tanggal)

class AttachmentSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     *
     * @return void
     */
    public function run(): void
    {
        // Data lampiran dari db_ticketing.sql
        // Sesuaikan 'ticket_no' dengan tiket yang sudah ada di database Anda setelah TicketSeeder berjalan
        $attachmentsData = [
            [
                'ticket_no' => 'REQ-00006', // Menggunakan ticket_no untuk mencari tiket terkait
                'file_name' => '862494283687164257702c9.76083056.xlsx',
                'created_at' => '2025-07-11 12:21:09',
            ],
            [
                'ticket_no' => 'REQ-00006',
                'file_name' => '160090324068716425781cd9.17203170.xlsx',
                'created_at' => '2025-07-11 12:21:09',
            ],
            // Tambahkan data lampiran lain jika ada di db_ticketing.sql Anda
            // Pastikan ticket_no sesuai dengan data yang Anda masukkan di TicketSeeder
        ];

        foreach ($attachmentsData as $attachmentData) {
            // Cari tiket berdasarkan ticket_no
            $ticket = Ticket::where('ticket_no', $attachmentData['ticket_no'])->first();

            // Jika tiket ditemukan, baru buat lampirannya
            if ($ticket) {
                // firstOrCreate akan membuat lampiran hanya jika kombinasi ticket_id dan file_name belum ada
                Attachment::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'file_name' => $attachmentData['file_name']
                    ],
                    [
                        'created_at' => Carbon::parse($attachmentData['created_at']),
                        'updated_at' => Carbon::parse($attachmentData['created_at']), // Gunakan created_at juga untuk updated_at jika tidak ada data terpisah
                    ]
                );
            } else {
                // Opsional: Log error jika tiket tidak ditemukan.
                // Ini penting jika ada data lampiran yang tidak memiliki tiket yang sesuai.
                // \Log::warning("Tiket dengan ticket_no '{$attachmentData['ticket_no']}' tidak ditemukan untuk lampiran: {$attachmentData['file_name']}");
            }
        }
    }
}
