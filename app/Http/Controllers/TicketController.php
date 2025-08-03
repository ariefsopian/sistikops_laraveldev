<?php // Pastikan tidak ada spasi atau baris kosong di atas baris ini

namespace App\Http\Controllers; // Ini harus menjadi pernyataan pertama setelah <?php

use App\Models\Ticket;
use App\Models\User;
use App\Models\Attachment;
use App\Models\TicketLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Untuk helper string (misal Str::slug)
use Symfony\Component\HttpFoundation\StreamedResponse; // Untuk export
use Illuminate\Support\Facades\DB; // Penting untuk transaksi database dan mengatasi "DB not found"

class TicketController extends Controller
{
    /**
     * Tampilkan daftar tiket.
     * Menggantikan index.php dari aplikasi lama.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        $tickets = Ticket::with(['requester', 'assignee']); // Eager load relasi requester dan assignee

        // Terapkan filter hak akses: jika bukan admin, batasi data yang bisa dilihat
        if (!$is_admin) {
            $tickets->where(function ($query) use ($user) {
                $query->where('requester_id', $user->id)
                      ->orWhere('assignee_id', $user->id);
            });
        }

        // Logika Filter dari Request (mirip dengan $_GET di index.php)
        if ($request->filled('filter_tanggal_mulai')) {
            $tickets->whereDate('created_at', '>=', $request->filter_tanggal_mulai);
        }
        if ($request->filled('filter_tanggal_akhir')) {
            $tickets->whereDate('created_at', '<=', $request->filter_tanggal_akhir);
        }
        if ($request->filled('filter_status')) {
            $tickets->where('status', $request->filter_status);
        }
        if ($request->filled('filter_priority')) {
            $tickets->where('priority', $request->filter_priority);
        }
        if ($request->filled('filter_requester')) {
            $tickets->where('requester_id', $request->filter_requester);
        }

        // Urutkan tiket: yang aktif di atas, yang selesai di bawah. Lalu urut berdasarkan jatuh tempo.
        $tickets->orderByRaw("CASE WHEN status IN ('Resolved', 'Closed') THEN 2 ELSE 1 END")
                ->orderBy('due_date', 'asc')
                ->orderBy('id', 'desc');

        $allUsersForFilter = User::orderBy('username')->get();
        $tickets = $tickets->paginate(20); // Paginasi otomatis

        // Flash notifikasi ke session (akan diambil di layout app.blade.php)
        $notification = null;
        if (session()->has('success_message')) {
            $notification = ['type' => 'success', 'title' => 'Berhasil!', 'message' => session('success_message')];
        } elseif (session()->has('error_message')) {
            $notification = ['type' => 'error', 'title' => 'Error!', 'message' => session('error_message')];
        }

        return view('tickets.index', compact('tickets', 'allUsersForFilter', 'user', 'notification'));
    }

    /**
     * Tampilkan form untuk membuat tiket baru.
     * Menggantikan tambah.php dari aplikasi lama.
     */
    public function create()
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Hak Akses: Hanya Requester atau Admin yang boleh membuat tiket
        if (!$user->isRequester() && !$is_admin) {
            abort(403, "Akses ditolak. Anda tidak memiliki izin untuk membuat tiket baru.");
        }

        // Ambil ID berikutnya untuk tampilan (nomor final dibuat saat simpan)
        // Logika ini hanya untuk display, tidak digunakan untuk generate ticket_no final
        $next_id_display = Ticket::max('id') + 1; // Ini hanya untuk tampilan, bukan untuk generate nomor tiket unik

        // Ambil daftar user yang bisa menjadi Assignee (Admin atau Assignee)
        $assignee_list = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Assignee']);
        })->orderBy('username')->get();

        // Jika admin, ambil semua user untuk dropdown requester
        $all_users_list = [];
        if ($is_admin) {
            $all_users_list = User::orderBy('username')->get();
        }

        return view('tickets.create', compact('next_id_display', 'assignee_list', 'all_users_list', 'user'));
    }

    /**
     * Simpan tiket baru ke database.
     * Menggantikan proses.php?aksi=tambah dari aplikasi lama.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Validasi input menggunakan Laravel Validation
        $request->validate([
            'requester_id' => 'required|exists:users,id',
            'project' => 'nullable|string|max:255', // Kolom 'project' ditambahkan validasinya
            'type' => 'required|string|in:Request,Incident,Change Request',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:Low,Medium,High',
            'status' => 'required|string|in:Open,In Progress',
            'due_date' => 'required|date',
            'assignee_id' => 'nullable|exists:users,id',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,docx,xlsx,csv,gif,bmp,webp,mp4,mov,webm', // Maks 5MB per file
        ]);

        // Hak Akses: Pastikan requester_id sesuai dengan user yang login jika bukan admin
        if (!$is_admin && $request->requester_id != $user->id) {
            return back()->withInput()->with('error_message', 'Anda tidak memiliki izin untuk membuat tiket atas nama user lain.');
        }

        // Mulai transaksi database untuk memastikan atomisitas operasi
        return DB::transaction(function () use ($request, $user) {
            // --- LOGIKA UNTUK GENERATE ticket_no YANG UNIK DAN AKURAT ---
            $prefix_map = [
                'Request' => 'REQ-',
                'Incident' => 'INC-',
                'Change Request' => 'CR-'
            ];
            $current_prefix = $prefix_map[$request->type] ?? 'TKT-';

            // Ambil semua ticket_no yang ada dengan prefix yang sama
            $existingTicketNumbers = Ticket::where('ticket_no', 'like', $current_prefix . '%')
                                           ->pluck('ticket_no');

            $max_numeric_part = 0;
            foreach ($existingTicketNumbers as $existingTicketNo) {
                // Ekstrak bagian numerik dari setiap ticket_no yang ada
                $numeric_part = (int) substr($existingTicketNo, strlen($current_prefix));
                if ($numeric_part > $max_numeric_part) {
                    $max_numeric_part = $numeric_part;
                }
            }

            $next_numeric_id = $max_numeric_part + 1;
            $padded_id = str_pad($next_numeric_id, 5, '0', STR_PAD_LEFT);
            $ticket_no = $current_prefix . $padded_id;
            // --- AKHIR LOGIKA GENERATE TICKET_NO ---


            // Buat record tiket baru di database
            $ticket = Ticket::create([
                'ticket_no' => $ticket_no, // Gunakan ticket_no yang sudah di-generate unik
                'requester_id' => $request->requester_id,
                'project' => $request->project, // Kolom 'project' disimpan di sini
                'type' => $request->type,
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'assignee_id' => $request->assignee_id,
            ]);

            // Handle unggahan file lampiran
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        // Buat nama file unik dan bersih
                        $fileName = uniqid('att_', true) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                        // Simpan file ke storage/app/public/uploads
                        $path = $file->storeAs('uploads', $fileName, 'public');

                        // Buat record lampiran di database dan kaitkan dengan tiket
                        $ticket->attachments()->create(['file_name' => $fileName]);
                    }
                }
            }

            // Redirect ke halaman daftar tiket dengan pesan sukses
            return redirect()->route('tickets.index')->with('success_message', 'Tiket baru berhasil ditambahkan!');
        });
    }

    /**
     * Tampilkan detail tiket.
     * Menggantikan detail.php dari aplikasi lama.
     * Menggunakan Route Model Binding: Laravel secara otomatis menemukan Ticket berdasarkan ID.
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Cek hak akses: admin, requester tiket, atau assignee tiket bisa melihat
        if (!$is_admin && $ticket->requester_id != $user->id && $ticket->assignee_id != $user->id) {
            abort(403, "Akses ditolak. Anda tidak memiliki izin untuk melihat tiket ini.");
        }

        // Eager load relasi untuk attachments dan logs agar tidak ada N+1 query problem
        $ticket->load('attachments', 'logs');

        return view('tickets.show', compact('ticket', 'user'));
    }

    /**
     * Tampilkan form untuk mengedit tiket.
     * Menggantikan edit.php dari aplikasi lama.
     * Menggunakan Route Model Binding.
     */
    public function edit(Ticket $ticket)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Cek hak akses: admin, requester tiket, atau assignee tiket bisa mengedit
        if (!$is_admin && $ticket->requester_id != $user->id && $ticket->assignee_id != $user->id) {
            abort(403, "Akses ditolak. Anda tidak memiliki izin untuk mengedit tiket ini.");
        }

        // Ambil daftar user yang bisa menjadi Assignee (Admin atau Assignee)
        $assignee_list = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Assignee']);
        })->orderBy('username')->get();

        // Jika admin, ambil semua user untuk dropdown requester
        $all_users_list = [];
        if ($is_admin) {
            $all_users_list = User::orderBy('username')->get();
        }

        // Eager load attachments untuk ditampilkan di form edit
        $ticket->load('attachments');

        return view('tickets.edit', compact('ticket', 'assignee_list', 'all_users_list', 'user'));
    }

    /**
     * Perbarui tiket di database.
     * Menggantikan proses.php?aksi=edit dari aplikasi lama.
     * Menggunakan Route Model Binding.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Cek hak akses
        if (!$is_admin && $ticket->requester_id != $user->id && $ticket->assignee_id != $user->id) {
            abort(403, "Akses ditolak. Anda tidak memiliki izin untuk mengedit tiket ini.");
        }

        // Validasi input
        $request->validate([
            'requester_id' => 'required|exists:users,id',
            'project' => 'nullable|string|max:255', // Kolom 'project' ditambahkan validasinya
            'type' => 'required|string|in:Request,Incident,Change Request',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:Low,Medium,High',
            'status' => 'required|string|in:Open,In Progress,Pending Approval,Resolved,Closed',
            'due_date' => 'required|date',
            'assignee_id' => 'nullable|exists:users,id',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,docx,xlsx,csv,gif,bmp,webp,mp4,mov,webm',
        ]);

        return DB::transaction(function () use ($request, $ticket, $user) {
            // Perbarui ticket_no jika tipe tiket berubah
            $new_ticket_no = $ticket->ticket_no;
            $current_prefix = explode('-', $ticket->ticket_no)[0] . '-';
            $new_prefix_map = [
                'Request' => 'REQ-',
                'Incident' => 'INC-',
                'Change Request' => 'CR-'
            ];
            $new_prefix = $new_prefix_map[$request->type] ?? 'TKT-';

            if ($new_prefix !== $current_prefix) {
                // Jika prefix berubah, kita bisa biarkan nomornya tetap atau membuat logika yang lebih canggih
                // Untuk kesederhanaan, kita hanya akan mengubah prefix jika tipe berubah, tapi nomor urut tetap
                $number_part = preg_replace('/^[A-Z]+-/', '', $ticket->ticket_no);
                $new_ticket_no = $new_prefix . $number_part;
            }

            // Perbarui record tiket di database
            $ticket->update([
                'ticket_no' => $new_ticket_no, // Update jika berubah
                'requester_id' => $request->requester_id,
                'project' => $request->project, // Kolom 'project' diperbarui di sini
                'type' => $request->type,
                'subject' => $request->subject,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'priority' => $request->priority,
                'status' => $request->status,
                'assignee_id' => $request->assignee_id,
            ]);

            // Handle penambahan file lampiran baru
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $fileName = uniqid('att_', true) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('uploads', $fileName, 'public');

                        $ticket->attachments()->create(['file_name' => $fileName]);
                    }
                }
            }

            // Redirect ke halaman daftar tiket dengan pesan sukses
            return redirect()->route('tickets.index')->with('success_message', 'Tiket berhasil diupdate!');
        });
    }

    /**
     * Hapus tiket dari database.
     * Menggantikan proses.php?aksi=hapus_tiket dari aplikasi lama.
     * Menggunakan Route Model Binding.
     */
    public function destroy(Ticket $ticket)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Cek hak akses: Hanya admin atau requester tiket yang bisa menghapus
        if (!$is_admin && $ticket->requester_id != $user->id) {
            abort(403, "Akses ditolak. Anda tidak memiliki izin untuk menghapus tiket ini.");
        }

        return DB::transaction(function () use ($ticket) {
            // Hapus file lampiran fisik terlebih dahulu
            foreach ($ticket->attachments as $attachment) {
                if (Storage::disk('public')->exists('uploads/' . $attachment->file_name)) {
                    Storage::disk('public')->delete('uploads/' . $attachment->file_name);
                }
            }

            // Hapus tiket dari database.
            // Ini akan otomatis menghapus attachments dan logs terkait karena onDelete('cascade')
            // yang sudah kita definisikan di file migrasi.
            $ticket->delete();

            // Redirect ke halaman daftar tiket dengan pesan sukses
            return redirect()->route('tickets.index')->with('success_message', 'Tiket berhasil dihapus.');
        });
    }

    /**
     * Tambahkan komentar/log aktivitas ke tiket.
     * Menggantikan proses.php?aksi=tambah_komentar dari aplikasi lama.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Cek hak akses: admin, requester tiket, atau assignee tiket bisa menambah komentar
        if (!$is_admin && $ticket->requester_id != $user->id && $ticket->assignee_id != $user->id) {
            return back()->with('error_message', 'Akses ditolak. Anda tidak memiliki izin untuk menambah komentar ke tiket ini.');
        }

        // Validasi input komentar
        $request->validate([
            'komentar' => 'required|string',
        ]);

        // Buat record log baru di database dan kaitkan dengan tiket
        $ticket->logs()->create([
            'user' => $user->username, // Ambil username dari user yang sedang login
            'log_text' => $request->komentar,
        ]);

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success_message', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Export daftar tiket ke file Excel (CSV).
     * Menggantikan export.php dari aplikasi lama.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        $tickets = Ticket::with(['requester', 'assignee']);

        // Terapkan filter hak akses yang sama seperti di index()
        if (!$is_admin) {
            $tickets->where(function ($query) use ($user) {
                $query->where('requester_id', $user->id)
                      ->orWhere('assignee_id', $user->id);
            });
        }

        // Terapkan filter dari request
        if ($request->filled('filter_tanggal_mulai')) {
            $tickets->whereDate('created_at', '>=', $request->filter_tanggal_mulai);
        }
        if ($request->filled('filter_tanggal_akhir')) {
            $tickets->whereDate('created_at', '<=', $request->filter_tanggal_akhir);
        }
        if ($request->filled('filter_status')) {
            $tickets->where('status', $request->filter_status);
        }
        if ($request->filled('filter_priority')) {
            $tickets->where('priority', $request->filter_priority);
        }
        if ($request->filled('filter_requester')) {
            $tickets->where('requester_id', $request->filter_requester);
        }

        $tickets = $tickets->orderBy('id', 'desc')->get(); // Ambil semua data tanpa paginasi untuk export

        $filename = 'Daftar_Tiket_SISTIKOPS_'.now()->format('Y-m-d').'.csv'; // Nama file export

        // Header HTTP untuk memicu unduhan file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Callback untuk menghasilkan konten CSV secara streaming
        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            // Tulis header kolom ke file CSV
            fputcsv($file, [
                'ID', 'No. Tiket', 'ID Pelapor', 'Nama Pelapor', 'Proyek', // Kolom 'Proyek' ditambahkan di header CSV
                'Tipe', 'Subjek', 'Deskripsi', 'Tanggal Jatuh Tempo', 'Prioritas',
                'Status', 'ID Assignee', 'Nama Assignee', 'Tanggal Dibuat'
            ]);

            // Tulis data tiket baris per baris
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->id,
                    $ticket->ticket_no,
                    $ticket->requester_id,
                    $ticket->requester->username ?? 'N/A', // Akses relasi requester
                    $ticket->project, // Kolom 'project' ditambahkan di data CSV
                    $ticket->type,
                    $ticket->subject,
                    $ticket->description,
                    $ticket->due_date,
                    $ticket->priority,
                    $ticket->status,
                    $ticket->assignee_id,
                    $ticket->assignee->username ?? 'N/A', // Akses relasi assignee
                    $ticket->created_at,
                ]);
            }
            fclose($file);
        };

        // Mengembalikan response streaming
        return response()->stream($callback, 200, $headers);
    }
}
