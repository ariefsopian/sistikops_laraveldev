<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk facade autentikasi

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     * Menggantikan login.php [cite: uploaded:sistikops/login.php]
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Anda akan membuat view ini di Tahap 6
    }

    /**
     * Tangani proses login.
     * Menggantikan proses_login.php [cite: uploaded:sistikops/proses_login.php]
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Ambil peran user yang baru login
            $user = Auth::user();
            // Simpan peran ke session (jika diperlukan untuk akses cepat, meskipun bisa diakses via $user->roles)
            // Note: Laravel Auth secara otomatis menyimpan user object di session
            // Anda bisa menambahkan logika custom role check di model User (isAdmin, isRequester, dll)
            // dan menggunakannya di middleware atau view
            
            return redirect()->intended('/dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Tangani proses logout.
     * Menggantikan logout.php [cite: uploaded:sistikops/logout.php]
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}