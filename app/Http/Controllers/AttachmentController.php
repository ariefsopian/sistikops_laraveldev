<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttachmentController extends Controller
{
    /**
     * Hapus lampiran dari database dan storage.
     * Menggantikan usermgmt/proses_hapus_lampiran.php [cite: uploaded:sistikops/usermgmt/proses_hapus_lampiran.php]
     * dan bagian hapus lampiran di proses.php [cite: uploaded:sistikops/proses.php]
     */
    public function destroy(Attachment $attachment)
    {
        $user = Auth::user();
        $is_admin = $user->isAdmin();

        // Dapatkan tiket terkait untuk cek hak akses
        $ticket = $attachment->ticket;

        // Cek hak akses: admin, requester tiket, atau assignee tiket
        if (!$is_admin && $ticket->requester_id != $user->id && $ticket->assignee_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin untuk menghapus lampiran ini.'], 403);
        }

        return DB::transaction(function () use ($attachment) {
            // Hapus file fisik
            if (Storage::disk('public')->exists('uploads/' . $attachment->file_name)) {
                Storage::disk('public')->delete('uploads/' . $attachment->file_name);
            }

            // Hapus record dari database
            $attachment->delete();

            return response()->json(['success' => true, 'message' => 'Lampiran berhasil dihapus.']);
        });
    }
}