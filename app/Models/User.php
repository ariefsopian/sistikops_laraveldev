<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Opsional, tergantung apakah Anda menggunakan verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Opsional, jika Anda menggunakan Laravel Sanctum untuk API token

// Pastikan baris-baris ini ada untuk mendefinisikan relasi Eloquent
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    // Menggunakan trait yang diperlukan
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut-atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', // Kolom username dari tabel users Anda
        'password', // Kolom password dari tabel users Anda
        // Jika Anda memiliki kolom lain seperti 'name' atau 'email' di tabel users, tambahkan di sini
        // 'name',
        // 'email',
    ];

    /**
     * Atribut-atribut yang harus disembunyikan saat serialisasi.
     * (Misalnya, saat mengonversi model ke array atau JSON)
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut-atribut yang harus di-cast ke tipe data tertentu.
     * Catatan: 'password' => 'hashed' dihapus karena Anda memasukkan hash langsung dari seeder
     * dan cast 'hashed' hanya ada di Laravel 10+ untuk hash otomatis saat setAttribute.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Biarkan ini jika Anda menggunakan verifikasi email
        // 'password' => 'hashed', // BARIS INI DIKOMENTARI/DIHAPUS UNTUK MENGATASI ERROR SEEDER
    ];

    /**
     * Definisikan relasi many-to-many dengan model Role.
     * User memiliki banyak Role, dan Role dimiliki oleh banyak User,
     * melalui tabel pivot 'user_roles'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        // Parameter: Model terkait, nama tabel pivot, foreign key model ini di tabel pivot, foreign key model terkait di tabel pivot
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Definisikan relasi one-to-many dengan model Ticket sebagai requester.
     * User dapat membuat banyak Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketsRequested(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    /**
     * Definisikan relasi one-to-many dengan model Ticket sebagai assignee.
     * User dapat ditugaskan ke banyak Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketsAssigned(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    /**
     * Memeriksa apakah user memiliki peran 'Admin'.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->roles->contains('name', 'Admin');
    }

    /**
     * Memeriksa apakah user memiliki peran 'Requester'.
     *
     * @return bool
     */
    public function isRequester(): bool
    {
        return $this->roles->contains('name', 'Requester');
    }

    /**
     * Memeriksa apakah user memiliki peran 'Assignee'.
     *
     * @return bool
     */
    public function isAssignee(): bool
    {
        return $this->roles->contains('name', 'Assignee');
    }

    // Anda bisa menambahkan metode helper lain di sini sesuai kebutuhan
}
