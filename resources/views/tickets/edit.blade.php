@extends('layouts.app')

@section('content')
<main class="container my-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h4 class="mb-0">Edit Tiket #{{ $ticket->ticket_no }}</h4>
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
            @if (session('error_message'))
                <div class="alert alert-danger">{{ session('error_message') }}</div>
            @endif

            <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" id="edit-ticket-form">
                @csrf {{-- Proteksi CSRF --}}
                @method('PUT') {{-- Direktif Blade untuk metode HTTP PUT/PATCH --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold" for="requester_select">Pelapor (Requester)</label>
                        @if ($user->isAdmin())
                            <select name="requester_id" id="requester_select" class="form-select" required>
                                @foreach ($all_users_list as $user_option)
                                <option value="{{ $user_option->id }}" {{ (old('requester_id', $ticket->requester_id) == $user_option->id) ? 'selected' : '' }}>
                                    {{ $user_option->username }}
                                </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ $ticket->requester->username ?? 'N/A' }}" readonly>
                            <input type="hidden" name="requester_id" value="{{ $ticket->requester_id }}">
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nomor Tiket</label>
                        <input type="text" class="form-control" value="{{ $ticket->ticket_no }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="project" class="form-label fw-bold">Proyek</label>
                    <input type="text" name="project" id="project" class="form-control" value="{{ old('project', $ticket->project) }}" placeholder="Nama Proyek (Opsional)">
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label fw-bold">Subjek</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject', $ticket->subject) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Deskripsi</label>
                    <textarea id="description" name="description" rows="5" class="form-control">{{ old('description', $ticket->description) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label fw-bold">Jenis Tiket</label>
                        <select id="type" name="type" class="form-select" required>
                            <option value="Incident" {{ (old('type', $ticket->type) == 'Incident') ? 'selected' : '' }}>Incident</option>
                            <option value="Request" {{ (old('type', $ticket->type) == 'Request') ? 'selected' : '' }}>Request</option>
                            <option value="Change Request" {{ (old('type', $ticket->type) == 'Change Request') ? 'selected' : '' }}>Change Request</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="Open" {{ (old('status', $ticket->status) == 'Open') ? 'selected' : '' }}>Open</option>
                            <option value="In Progress" {{ (old('status', $ticket->status) == 'In Progress') ? 'selected' : '' }}>In Progress</option>
                            <option value="Pending Approval" {{ (old('status', $ticket->status) == 'Pending Approval') ? 'selected' : '' }}>Pending Approval</option>
                            <option value="Resolved" {{ (old('status', $ticket->status) == 'Resolved') ? 'selected' : '' }}>Resolved</option>
                            <option value="Closed" {{ (old('status', $ticket->status) == 'Closed') ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label fw-bold">Prioritas</label>
                        <select id="priority" name="priority" class="form-select" required>
                            <option value="Low" {{ (old('priority', $ticket->priority) == 'Low') ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ (old('priority', $ticket->priority) == 'Medium') ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ (old('priority', $ticket->priority) == 'High') ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="assignee_id" class="form-label fw-bold">Penanggung Jawab (Assignee)</label>
                        <select id="assignee_id" name="assignee_id" class="form-select">
                            <option value="">-- Pilih Assignee --</option>
                            @foreach($assignee_list as $assignee_user)
                            <option value="{{ $assignee_user->id }}" {{ (old('assignee_id', $ticket->assignee_id) == $assignee_user->id) ? 'selected' : '' }}>
                                {{ $assignee_user->username }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="due_date" class="form-label fw-bold">Tanggal Jatuh Tempo</label>
                        {{-- PERBAIKI BARIS INI: Gunakan old() dengan fallback ke $ticket->due_date --}}
                        <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date', $ticket->due_date ? $ticket->due_date->format('Y-m-d') : '') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lampiran Saat Ini</label>
                    @if ($ticket->attachments->isEmpty())
                        <p class="text-muted">Tidak ada lampiran.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($ticket->attachments as $attachment)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ asset('storage/uploads/' . $attachment->file_name) }}" target="_blank">
                                        <i class="fas fa-paperclip me-2"></i>{{ $attachment->file_name }}
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-attachment-js"
                                            data-ticket-id="{{ $ticket->id }}"
                                            data-attachment-id="{{ $attachment->id }}"
                                            title="Hapus Lampiran">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="attachments" class="form-label fw-bold">Tambah Lampiran Baru</label>
                    <input type="file" name="attachments[]" class="form-control" id="attachments" multiple>
                    <div class="form-text">Pilih file baru jika ingin menambah. Maks 5MB per file.</div>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#requester_select, #assignee_id').select2({
        theme: "bootstrap-5",
        placeholder: "Cari dan pilih nama"
    });

    $('#edit-ticket-form').on('submit', function(e) {
        let isValid = true;
        let firstInvalidField = null;

        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if ($(this).val() == null || $(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
                if (!firstInvalidField) {
                    firstInvalidField = $(this);
                }
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Form Belum Lengkap',
                text: 'Harap isi semua field yang wajib diisi.',
            });
            if(firstInvalidField) {
                firstInvalidField.focus();
            }
        }
    });

    $(document).on('click', '.btn-delete-attachment-js', function(e) {
        e.preventDefault();

        var button = $(this);
        var ticketId = button.data('ticket-id');
        var attachmentId = button.data('attachment-id');

        Swal.fire({
            title: 'Anda yakin?',
            text: "Lampiran ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_APP_PATH + 'attachments/' + attachmentId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}',
                        ticket_id: ticketId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus lampiran: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
                        });
                    }
                });
            }
        });
    });


    const fileInput = document.getElementById('attachments');
    if (fileInput) {
        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;
            const maxSizeInBytes = 5 * 1024 * 1024; // 5 MB
            const oversizedFiles = [];
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > maxSizeInBytes) {
                    oversizedFiles.push(files[i].name);
                }
            }
            if (oversizedFiles.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan Ukuran File',
                    text: 'Ukuran file berikut melebihi batas 5 MB dan tidak akan diunggah:\n\n' + oversizedFiles.join('\n'),
                });
            }
        });
    }
});
</script>
@endpush
