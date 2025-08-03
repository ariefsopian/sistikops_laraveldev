<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import untuk relasi BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import untuk relasi HasMany
use App\Models\TicketLog; // Penting: Import model TicketLog untuk relasi logs()

class Ticket extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'tickets'; // Pastikan nama tabel sesuai dengan migrasi Anda

    /**
     * Atribut-atribut yang dapat diisi secara massal (mass assignable).
     * Kolom 'project' ditambahkan di sini.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_no',
        'requester_id',
        'project', // <--- KOLOM 'project' DITAMBAHKAN DI SINI
        'type',
        'subject',
        'description',
        'due_date',
        'priority',
        'status',
        'assignee_id',
        // created_at dan updated_at diisi otomatis oleh timestamps()
    ];

    /**
     * Atribut-atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date', // Mengubah due_date menjadi objek Carbon secara otomatis
        'created_at' => 'datetime', // Mengubah created_at menjadi objek Carbon
        'updated_at' => 'datetime', // Mengubah updated_at menjadi objek Carbon
    ];

    /**
     * Definisikan relasi: Ticket dimiliki oleh satu Requester (User).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requester(): BelongsTo
    {
        // Ticket belongs to a User (requester) via 'requester_id'
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Definisikan relasi: Ticket dimiliki oleh satu Assignee (User).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee(): BelongsTo
    {
        // Ticket belongs to a User (assignee) via 'assignee_id'
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Definisikan relasi: Ticket memiliki banyak Attachment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments(): HasMany
    {
        // Ticket has many Attachments
        return $this->hasMany(Attachment::class, 'ticket_id');
    }

    /**
     * Definisikan relasi: Ticket memiliki banyak TicketLog (komentar/aktivitas).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(): HasMany
    {
        // Ticket has many TicketLogs
        return $this->hasMany(TicketLog::class, 'ticket_id');
    }
}
