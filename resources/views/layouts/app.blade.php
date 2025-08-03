<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page_title ?? "Aplikasi SISTIKOPS 2025" }}</title>

    {{-- Sertakan CSS dari CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    {{-- Sertakan CSS lokal dari direktori public --}}
    {{-- Pastikan file style.css Anda sudah disalin ke public/css/style.css --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Sertakan jQuery dan Select2 JS dari CDN (diletakkan di head karena Select2 butuh jQuery segera) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Variabel global JS untuk path aplikasi, mirip BASE_APP_PATH di koneksi.php lama Anda --}}
    <script>
        const BASE_APP_PATH = "{{ url('/') }}/";
    </script>
</head>
<body>
    {{-- Sertakan partial untuk Navbar --}}
    @include('partials.navbar')

    <div class="container mt-3">
        {{-- Area untuk notifikasi SweetAlert --}}
        {{-- Ini akan menampilkan pesan sukses atau error yang di-flash dari controller --}}
        @if (session('success_message'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success_message') }}',
                    timer: 2500,
                    showConfirmButton: false
                });
            </script>
        @endif
        @if (session('error_message'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error_message') }}',
                    timer: 2500,
                    showConfirmButton: false
                });
            </script>
        @endif
    </div>

    <main class="container my-4">
        {{-- Konten dari setiap halaman individual akan dimasukkan di sini --}}
        @yield('content')
    </main>

    {{-- Sertakan partial untuk Footer --}}
    @include('partials.footer')

    {{-- Sertakan Bootstrap JS dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Sertakan SweetAlert2 JS dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Area untuk skrip JavaScript khusus halaman individual --}}
    @stack('scripts')
</body>
</html>
