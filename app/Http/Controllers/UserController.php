<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang sedang login

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna.
     * Menggantikan usermgmt/manajemen_user.php [cite: uploaded:sistikops/usermgmt/manajemen_user.php]
     */
    public function index()
    {
        // Pastikan hanya admin yang bisa mengakses ini (sudah di handle di routes/web.php dengan middleware 'can:manage-users')
        $users = User::with('roles')->orderBy('id', 'asc')->get();
        $all_roles = Role::orderBy('name', 'asc')->get();
        $current_user_id = Auth::id(); // Dapatkan ID user yang sedang login

        return view('users.index', compact('users', 'all_roles', 'current_user_id'));
    }

    /**
     * Simpan user baru ke database.
     * Menggantikan usermgmt/proses_user.php?aksi=tambah [cite: uploaded:sistikops/usermgmt/proses_user.php]
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:6', // Minimal 6 karakter
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id', // Pastikan ID role valid
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password), // Hash password sebelum disimpan
            ]);

            $user->roles()->attach($request->roles); // Melampirkan role ke user

            return redirect()->route('users.index')->with('success_message', 'User berhasil ditambahkan!');
        });
    }

    /**
     * Tampilkan form untuk mengedit user.
     * Menggantikan usermgmt/edit_user.php [cite: uploaded:sistikops/usermgmt/edit_user.php]
     */
    public function edit(User $user)
    {
        $all_roles = Role::orderBy('name', 'asc')->get();
        $user_role_ids = $user->roles->pluck('id')->toArray(); // Ambil ID role yang dimiliki user
        $current_user_id = Auth::id();

        return view('users.edit', compact('user', 'all_roles', 'user_role_ids', 'current_user_id'));
    }

    /**
     * Perbarui user di database.
     * Menggantikan usermgmt/proses_user.php?aksi=edit [cite: uploaded:sistikops/usermgmt/proses_user.php]
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $user->id, // Unique kecuali untuk user ini sendiri
            'password' => 'nullable|string|min:6',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        return DB::transaction(function () use ($request, $user) {
            $userData = ['username' => $request->username];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password); // Hash password baru
            }

            $user->update($userData);

            // Update roles (admin tidak bisa mengubah role diri sendiri)
            if ($user->id != Auth::id()) {
                $user->roles()->sync($request->roles); // Sinkronkan role
            } else {
                // Jika admin mencoba mengubah role dirinya sendiri, Laravel akan mengabaikan perubahan role
                // karena input disabled di view. Namun, jika ada validasi lain, ini penting.
                // Anda bisa menambahkan logika khusus di sini jika perlu.
            }

            return redirect()->route('users.index')->with('success_message', 'User berhasil diupdate!');
        });
    }

    /**
     * Hapus user dari database.
     * Menggantikan usermgmt/proses_user.php?aksi=hapus_user [cite: uploaded:sistikops/usermgmt/proses_user.php]
     */
    public function destroy(User $user)
    {
        // Cek hak akses: Pastikan user tidak menghapus akunnya sendiri
        if ($user->id == Auth::id()) {
            return back()->with('error_message', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        return DB::transaction(function () use ($user) {
            // Relasi user_roles akan otomatis dihapus karena onDelete('cascade') di migrasi
            // User akan dihapus
            $user->delete();

            return redirect()->route('users.index')->with('success_message', 'User berhasil dihapus!');
        });
    }
}