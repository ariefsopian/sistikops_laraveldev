<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import untuk relasi BelongsTo

class Attachment extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'attachments'; // Pastikan nama tabel sesuai

    /**
     * Atribut-atribut yang dapat diisi secara massal (mass assignable).
     * Tambahkan 'file_name' di sini.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id', // Kolom foreign key
        'file_name', // <--- TAMBAHKAN BARIS INI
    ];

    /**
     * Definisikan relasi: Attachment dimiliki oleh satu Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket(): BelongsTo
    {
        // Attachment belongs to a Ticket via 'ticket_id'
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
