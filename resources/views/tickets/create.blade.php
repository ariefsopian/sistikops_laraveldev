    @extends('layouts.app')

    @section('content')
    <main class="container my-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><h4 class="mb-0">Formulir Tiket Baru</h4></div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error_message'))
                    <div class="alert alert-danger">{{ session('error_message') }}</div>
                @endif

                <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" id="form-tiket">
                    @csrf

                    @if ($user->isAdmin())
                    <div class="mb-3">
                        <label for="requester_select" class="form-label fw-bold">Pelapor (Requester)</label>
                        <select name="requester_id" id="requester_select" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Pelapor --</option>
                            @foreach ($all_users_list as $user_option)
                            <option value="{{ $user_option->id }}" {{ old('requester_id') == $user_option->id ? 'selected' : '' }}>
                                {{ $user_option->username }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="requester_id" value="{{ $user->id }}">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pelapor (Requester)</label>
                            <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                        </div>
                    @endif

                    <div class="mb-3"> {{-- <--- TAMBAHKAN BAGIAN INI --}}
                        <label for="project" class="form-label fw-bold">Proyek</label>
                        <input type="text" name="project" id="project" class="form-control" value="{{ old('project') }}" placeholder="Nama Proyek (Opsional)">
                    </div>

                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-bold">Jenis Tiket</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="Request" data-prefix="REQ-" {{ old('type') == 'Request' ? 'selected' : '' }}>Request</option>
                                <option value="Incident" data-prefix="INC-" {{ old('type') == 'Incident' ? 'selected' : '' }}>Incident</option>
                                <option value="Change Request" data-prefix="CR-" {{ old('type') == 'Change Request' ? 'selected' : '' }}>Change Request</option>
                            </select>
                         </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor Tiket (Otomatis)</label>
                            <input type="text" id="ticket_no_display" class="form-control" placeholder="Pilih jenis tiket..." readonly>
                         </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Prioritas</label>
                            <select name="priority" class="form-select" required>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="assignee_select" class="form-label fw-bold">Penanggung Jawab (Assignee)</label>
                            <select name="assignee_id" id="assignee_select" class="form-select">
                                <option value="">-- Pilih Assignee --</option>
                                @foreach($assignee_list as $assignee_user)
                                    <option value="{{ $assignee_user->id }}" {{ old('assignee_id') == $assignee_user->id ? 'selected' : '' }}>
                                        {{ $assignee_user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subjek</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lampiran</label>
                        <input type="file" name="attachments[]" class="form-control" id="attachments" multiple>
                        <div class="form-text">Pilih file untuk dilampirkan. File akan diunggah saat Anda menekan tombol <strong>Simpan Tiket</strong> di bawah. Maks 5MB per file.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Open" {{ old('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Jatuh Tempo</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Tiket</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @endsection

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#requester_select, #assignee_select').select2({
                theme: "bootstrap-5",
                placeholder: "Cari dan pilih nama"
            });

            const nextId = {{ $next_id_display }};

            function updateTicketNumber() {
                const selectedOption = $('#type').find('option:selected');
                const prefix = selectedOption.data('prefix');
                const paddedId = String(nextId).padStart(5, '0');
                $('#ticket_no_display').val(prefix + paddedId);
            }

            updateTicketNumber();

            $('#type').on('change', function() {
                updateTicketNumber();
            });

            $('#form-tiket').on('submit', function(e) {
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
    