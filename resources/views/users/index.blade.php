@extends('layouts.app')

@section('content')
<main class="container my-4">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><h4 class="mb-0">Daftar Pengguna</h4></div>
                <div class="card-body">
                    {{-- Menampilkan pesan flash dari controller --}}
                    @if (session('success_message'))
                        <div class="alert alert-success" role="alert">{{ session('success_message') }}</div>
                    @endif
                    @if (session('error_message'))
                        <div class="alert alert-danger" role="alert">{{ session('error_message') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user_item)
                                <tr>
                                    <td>{{ $user_item->id }}</td>
                                    <td>{{ $user_item->username }}</td>
                                    <td>
                                        @if($user_item->roles->isNotEmpty())
                                            @foreach($user_item->roles as $role)
                                                @php
                                                    $badge_class = 'bg-secondary';
                                                    if ($role->name == 'Admin') $badge_class = 'bg-success';
                                                    if ($role->name == 'Assignee') $badge_class = 'bg-primary';
                                                    if ($role->name == 'Requester') $badge_class = 'bg-warning text-dark';
                                                    if ($role->name == 'User') $badge_class = 'bg-info text-dark';
                                                @endphp
                                                <span class="badge me-1 {{ $badge_class }}">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-light text-dark">Tanpa Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.edit', $user_item->id) }}" class="btn btn-outline-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                        {{-- Tidak bisa menghapus akun sendiri --}}
                                        @if ($user_item->id != $current_user_id)
                                            <button class="btn btn-outline-danger btn-sm btn-delete-user" data-id="{{ $user_item->id }}" data-username="{{ $user_item->username }}" title="Hapus"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Tidak ada pengguna ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><h4 class="mb-0">Tambah User Baru</h4></div>
                <div class="card-body">
                    {{-- Menampilkan error validasi untuk form tambah user --}}
                    @if ($errors->hasAny(['username', 'password', 'roles']))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="roles-tambah" class="form-label">Role</label>
                            <select class="form-select" id="roles-tambah" name="roles[]" multiple required>
                                @foreach($all_roles as $role)
                                    <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Tambah User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#roles-tambah').select2({
            theme: "bootstrap-5",
            placeholder: "Pilih satu atau lebih role"
        });

        // Handle tombol hapus user
        $('.btn-delete-user').on('click', function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            var username = $(this).data('username');

            Swal.fire({
                title: 'Anda yakin?',
                text: "User '" + username + "' akan dihapus. Aksi ini tidak bisa dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form dinamis untuk POST request DELETE
                    var form = $('<form action="' + BASE_APP_PATH + 'users/' + userId + '" method="POST"></form>');
                    form.append('@csrf');
                    form.append('@method('DELETE')');
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
