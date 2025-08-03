<nav class="navbar navbar-expand-lg navbar-dark bg-dark-custom shadow-lg rounded-bottom">
    <div class="container">
        {{-- Menggunakan route() helper untuk link ke dashboard --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            {{-- Ikon placeholder untuk logo --}}
            <i class="fas fa-ticket-alt me-2 text-primary-light"></i>
            SISTIKOPS 2025
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    {{-- Menambahkan kelas 'active' jika rute saat ini adalah 'dashboard' --}}
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    {{-- Menambahkan kelas 'active' jika rute saat ini dimulai dengan 'tickets.' --}}
                    {{-- Mengganti "Tiket" menjadi "Tickets" --}}
                    <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" aria-current="page" href="{{ route('tickets.index') }}">Tickets</a>
                </li>
                {{-- Cek apakah user memiliki peran 'Admin' menggunakan method dari model User --}}
                @if (Auth::check() && Auth::user()->isAdmin())
                <li class="nav-item">
                    {{-- Menambahkan kelas 'active' jika rute saat ini dimulai dengan 'users.' --}}
                    {{-- Mengganti "Kelola Pengguna" menjadi "Manage Users" --}}
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Manage Users</a>
                </li>
                @endif
                <li class="nav-item dropdown ms-lg-3">
                    {{-- Menampilkan username user yang sedang login dengan ikon --}}
                    <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm rounded-pill px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i> Hello, {{ Auth::user()->username ?? 'Pengguna' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            {{-- Form untuk logout, menggunakan route() helper dan proteksi CSRF --}}
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Tambahkan gaya kustom untuk navbar --}}
<style>
    .bg-dark-custom {
        background-color: #2c3e50; /* Warna biru gelap kustom */
    }
    .text-primary-light {
        color: #5dade2; /* Warna biru muda kustom */
    }
    .navbar-brand .fas {
        font-size: 1.5rem; /* Ukuran ikon logo */
    }
    .rounded-bottom {
        border-bottom-left-radius: 0.75rem !important;
        border-bottom-right-radius: 0.75rem !important;
    }
    .shadow-lg {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2) !important;
    }
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
</style>
