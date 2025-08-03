<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;   // Import model Ticket
use App\Models\User;    // Import model User
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang sedang login
use Illuminate\Support\Facades\DB;   // Untuk kueri database mentah jika diperlukan
use Carbon\Carbon; // Untuk manipulasi tanggal dan waktu

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard.
     * Menggantikan dashboard.php dari aplikasi lama.
     */
    public function index()
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin(); // Menggunakan method isAdmin() dari model User

        // Mulai kueri dasar untuk tiket.
        // Ini akan digunakan sebagai dasar untuk semua perhitungan statistik dan chart.
        $query = Ticket::query();

        // Terapkan filter hak akses: jika bukan admin, batasi data yang bisa dilihat
        if (!$is_admin) {
            $query->where(function ($q) use ($user) {
                $q->where('requester_id', $user->id)
                  ->orWhere('assignee_id', $user->id);
            });
        }

        // === 1. Query untuk Kartu Statistik ===

        // Total Tiket Berstatus 'Open'
        $total_open = (clone $query)->where('status', 'Open')->count();

        // Total Tiket Berstatus 'In Progress'
        $total_progress = (clone $query)->where('status', 'In Progress')->count();

        // Total Tiket Jatuh Tempo Hari Ini (yang masih aktif)
        $total_due_today = (clone $query)->whereDate('due_date', Carbon::today()->toDateString())
                                         ->whereNotIn('status', ['Resolved', 'Closed']) // Hanya tiket yang belum selesai
                                         ->count();

        // Total Tiket Ditugaskan ke User yang Sedang Login (yang masih aktif)
        // Kueri ini tidak perlu di-clone dari $query dasar jika $query dasar sudah difilter
        // berdasarkan user yang login, karena ini spesifik untuk assignee_id user tersebut.
        $total_my_tickets = Ticket::where('assignee_id', $user->id)
                                  ->whereNotIn('status', ['Resolved', 'Closed'])
                                  ->count();


        // === 2. Query untuk Chart ===

        // Data Tiket Berdasarkan Status (Pie Chart)
        $status_data = (clone $query)->select('status', DB::raw('COUNT(*) as jumlah'))
                                     ->groupBy('status')
                                     ->pluck('jumlah', 'status') // Mengambil 'jumlah' sebagai value dan 'status' sebagai key
                                     ->toArray();

        // Data Tiket Dibuat per Bulan (Bar Chart)
        // Perbaikan untuk error SQLSTATE[42000]: 1055 'created_at' isn't in GROUP BY
        $monthly_data = (clone $query)->select(
                                            DB::raw("DATE_FORMAT(created_at, '%b %Y') as bulan"), // Kolom untuk label bulan
                                            DB::raw('COUNT(*) as jumlah') // Kolom untuk jumlah tiket
                                          )
                                          // Penting: Semua ekspresi non-agregat di SELECT harus ada di GROUP BY
                                          ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                                          ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')")) // Urutkan berdasarkan tahun-bulan
                                          ->limit(12) // Ambil data untuk 12 bulan terakhir
                                          ->pluck('jumlah', 'bulan') // Mengambil 'jumlah' sebagai value dan 'bulan' sebagai key
                                          ->toArray();

        // Pemetaan warna status untuk chart (sesuai dengan aplikasi lama)
        $status_color_map = [
            'Open' => '#0d6efd',
            'In Progress' => '#0dcaf0',
            'Pending Approval' => '#ffc107',
            'Resolved' => '#198754',
            'Closed' => '#6c757d'
        ];
        $chart_status_labels = array_keys($status_data);
        $chart_status_data = array_values($status_data);
        // Memastikan warna sesuai dengan status yang ada
        $chart_status_colors = array_map(fn($status) => $status_color_map[$status] ?? '#adb5bd', $chart_status_labels);

        // Siapkan data bulanan untuk chart
        // monthly_labels sudah di-pluck sebagai key, jadi tinggal array_keys() dan array_values()
        $monthly_labels = array_keys($monthly_data);
        $monthly_values = array_values($monthly_data);


        // === 3. Query untuk Daftar Tiket di Dashboard ===

        // Tiket Prioritas (Jatuh tempo dalam 3 hari ATAU sudah terlambat)
        $priority_tickets = (clone $query)->where('due_date', '<=', Carbon::now()->addDays(3)->toDateString())
                                          ->whereNotIn('status', ['Resolved', 'Closed']) // Hanya tiket yang belum selesai
                                          ->orderBy('due_date', 'asc') // Urutkan berdasarkan tanggal jatuh tempo
                                          ->limit(5) // Ambil 5 tiket teratas
                                          ->get();

        // Tiket yang Ditugaskan ke User yang Sedang Login (yang masih aktif)
        $my_assigned_tickets = Ticket::with('requester') // Eager load relasi requester untuk mendapatkan username
                                     ->where('assignee_id', $user->id)
                                     ->whereNotIn('status', ['Resolved', 'Closed']) // Hanya tiket yang belum selesai
                                     ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal dibuat
                                     ->limit(5) // Ambil 5 tiket teratas
                                     ->get();

        // Kirim semua data yang telah diproses ke view
        return view('dashboard.index', compact(
            'total_open', 'total_progress', 'total_due_today', 'total_my_tickets',
            'chart_status_labels', 'chart_status_data', 'chart_status_colors',
            'monthly_labels', 'monthly_values', // Menggunakan monthly_values untuk data chart
            'priority_tickets', 'my_assigned_tickets'
        ));
    }
}
