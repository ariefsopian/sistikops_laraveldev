@extends('layouts.app')

@section('content')
<main class="container my-4">
    <div class="card shadow-sm edit-container">
        <div class="card-header"><h4 class="mb-0">Edit User: {{ $user->username }}</h4></div>
        <div class="card-body">
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
            @if (session('error_message'))
                <div class="alert alert-danger">{{ session('error_message') }}</div>
            @endif

             <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf {{-- Proteksi CSRF --}}
                @method('PUT') {{-- Direktif Blade untuk metode HTTP PUT/PATCH --}}

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                </div>
                <div class="mb-3">
                    <label for="roles" class="form-label">Role</label>
                    <select class="form-select" id="roles" name="roles[]" multiple required {{ $user->id == $current_user_id ? 'disabled' : '' }}>
                        @foreach ($all_roles as $role)
                            <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', $user_role_ids)) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                     @if ($user->id == $current_user_id)
                        <div class="form-text text-danger">Anda tidak dapat mengubah role diri sendiri.</div>
                    @endif
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#roles').select2({
            theme: "bootstrap-5",
            placeholder: "Pilih satu atau lebih role"
        });
    });
</script>
@endpush
