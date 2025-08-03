<?php

use Illuminate\Support\Facades\Route;
// Import semua Controller yang akan kita gunakan.
// Pastikan namespace dan nama kelasnya sesuai dengan Controller yang Anda buat di app/Http/Controllers/.
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttachmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dalam grup yang berisi middleware "web".
| Sekarang buat sesuatu yang hebat!
|
*/

// =========================================================================
// Rute Publik (Tidak Memerlukan Autentikasi/Login)
// =========================================================================

// Rute untuk menampilkan form login
// Menggantikan login.php
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Rute untuk memproses data login (saat form disubmit)
// Menggantikan proses_login.php
Route::post('/login', [LoginController::class, 'login']);

// Rute default jika user belum login atau mencoba mengakses root tanpa autentikasi,
// akan diarahkan ke halaman login.
Route::redirect('/', '/login');


// =========================================================================
// Rute Terautentikasi (Memerlukan Pengguna untuk Login)
// Semua rute di dalam grup ini hanya bisa diakses setelah user berhasil login.
// Middleware 'auth' akan secara otomatis mengarahkan user yang belum login ke rute 'login'.
// =========================================================================
Route::middleware('auth')->group(function () {

    // Rute untuk Logout
    // Menggantikan logout.php
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rute untuk Dashboard
    // Menggantikan dashboard.php
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Kustom untuk Export Data Tiket
    // PENTING: Rute ini harus didefinisikan SEBELUM Route::resource('tickets', ...)
    // untuk menghindari konflik dengan wildcard {ticket}.
    // Menggantikan export.php
    Route::get('tickets/export', [TicketController::class, 'export'])->name('tickets.export');

    // Rute untuk Manajemen Tiket
    // Route::resource() secara otomatis membuat 7 rute standar (index, create, store, show, edit, update, destroy)
    // untuk resource 'tickets'. Ini sangat efisien untuk operasi CRUD.
    // Menggantikan index.php, tambah.php, detail.php, edit.php, dan sebagian besar proses.php
    Route::resource('tickets', TicketController::class);

    // Rute Kustom untuk Menambah Komentar pada Tiket
    // Karena menambah komentar bukan bagian dari operasi CRUD standar, kita definisikan secara terpisah.
    // Menggantikan bagian tambah_komentar di proses.php
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.add_comment');

    // Rute untuk Penghapusan Lampiran
    // Menggantikan proses_hapus_lampiran.php dan bagian hapus_lampiran di proses.php
    // {attachment} adalah parameter wildcard yang akan menangkap ID lampiran.
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');


    // =========================================================================
    // Rute Manajemen Pengguna (Hanya untuk Admin)
    // =========================================================================
    // Middleware 'can:manage-users' akan memastikan hanya pengguna yang memiliki
    // kemampuan (Gate/Policy) 'manage-users' yang dapat mengakses rute-rute ini.
    // Ini adalah lapisan keamanan otorisasi.
    Route::middleware('can:manage-users')->group(function () {
        // Route::resource() untuk manajemen user:
        // Ini akan membuat rute CRUD standar untuk resource 'users'.
        // Menggantikan manajemen_user.php, edit_user.php, dan sebagian besar usermgmt/proses_user.php
        Route::resource('users', UserController::class);
    });

}); // Akhir dari grup rute yang memerlukan autentikasi
