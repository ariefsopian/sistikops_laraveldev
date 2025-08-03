<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Jika Anda ingin relasi ke Ticket

class TicketLog extends Model
{
    use HasFactory;

    protected $table = 'ticket_logs'; // Pastikan nama tabel sesuai migrasi

    protected $fillable = [
        'ticket_id',
        'user',
        'log_text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Jika Anda ingin relasi balik dari TicketLog ke Ticket
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}