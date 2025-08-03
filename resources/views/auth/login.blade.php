<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Aplikasi SISTIKOPS 2025</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Sertakan CSS lokal --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* === CSS dengan Tema Biru Muda === */
        body {
            /* Latar belakang biru muda yang lembut */
            background-color: #f0f8ff; /* AliceBlue */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .app-title {
            /* Header judul aplikasi dengan warna gelap agar kontras */
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50; /* Dark Blue-Gray */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        .login-card .card-header {
            /* Header kartu dengan warna biru muda yang lebih pekat */
            background-color: #5dade2; /* Light Blue */
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 1.5rem;
            font-size: 1.5rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(93, 173, 226, 0.3);
            border-color: #5dade2;
        }

        .btn-primary {
            /* Tombol login dengan warna biru yang serasi */
            background-color: #5dade2;
            border: none;
            padding: 0.75rem;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3498db; /* Warna biru saat hover */
        }

        .footer-text {
            /* Teks footer dengan warna gelap agar terbaca */
            color: #34495e;
            margin-top: 3rem;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="app-title">
        Aplikasi SISTIKOPS 2025
    </div>

    <div class="card login-card">
        <div class="card-header text-center">
            <h4><i class="fas fa-lock me-2"></i> User Login</h4>
        </div>
        <div class="card-body p-4">
            {{-- Menampilkan error validasi Laravel --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- Menampilkan pesan flash dari controller --}}
            @if (session('success_message'))
                <div class="alert alert-success">{{ session('success_message') }}</div>
            @endif
            @if (session('error_message'))
                <div class="alert alert-danger">{{ session('error_message') }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf {{-- Direktif Blade untuk proteksi CSRF --}}
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" value="{{ old('username') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer-text">
        © Copyright by Arief Sopian 2025. All Right Reserved
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // jQuery untuk fokus otomatis ke kolom username saat halaman dimuat
        $(document).ready(function() {
            $('#username').focus();
        });
    </script>
</body>
</html>
