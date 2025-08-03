    @extends('layouts.app')

    @section('content')
    <main class="container my-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Detail Tiket #{{ $ticket->ticket_no }}</h4>
                @if ($user->isAdmin() || $ticket->requester_id == $user->id || $ticket->assignee_id == $user->id)
                <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i> Edit Tiket</a>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-8 border-end pe-md-4">
                        <h5 class="fw-bold">{{ $ticket->subject }}</h5>
                        <p class="text-muted">
                            Dibuat oleh <strong>{{ $ticket->requester->username ?? 'N/A' }}</strong>
                            pada {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, H:i') }}
                        </p>
                        <p class="mb-2"><strong>Proyek:</strong> {{ $ticket->project ?? 'N/A' }}</p> {{-- <--- TAMBAHKAN INI --}}
                        <hr>
                        <h6>Deskripsi:</h6>
                        <p style="white-space: pre-wrap;">{{ !empty($ticket->description) ? nl2br(e($ticket->description)) : 'Tidak ada deskripsi.' }}</p>
                    </div>
                    <div class="col-md-4 ps-md-4">
                        <h6 class="border-bottom pb-2 mb-3">Rincian Tiket</h6>
                        <p class="mb-2"><strong>Status:</strong> <span class="badge rounded-pill {{ [
                            'Open'=>'bg-primary',
                            'In Progress'=>'bg-info text-dark',
                            'Pending Approval'=>'bg-warning text-dark',
                            'Resolved'=>'bg-success',
                            'Closed'=>'bg-secondary'
                        ][$ticket->status] ?? 'bg-light text-dark' }}">{{ $ticket->status }}</span></p>
                        <p class="mb-2"><strong>Prioritas:</strong> <span class="badge rounded-pill {{ [
                            'High'=>'bg-danger',
                            'Medium'=>'bg-warning text-dark',
                            'Low'=>'bg-success'
                        ][$ticket->priority] ?? 'bg-light text-dark' }}">{{ $ticket->priority }}</span></p>
                        <p class="mb-2"><strong>Assignee:</strong> {{ $ticket->assignee->username ?? 'Belum Ditugaskan' }}</p>
                        <p class="mb-2"><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y') }}</p>
                        <p class="mb-2"><strong>Tipe Tiket:</strong> {{ $ticket->type }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light"><h5 class="mb-0">Lampiran</h5></div>
                    <div class="card-body">
                        @if ($ticket->attachments->isEmpty())
                            <p class="text-muted">Tidak ada lampiran.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($ticket->attachments as $attachment)
                                    <li class="list-group-item">
                                        <a href="{{ asset('storage/uploads/' . $attachment->file_name) }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-paperclip me-2 text-muted"></i>{{ $attachment->file_name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                 <div class="card shadow-sm h-100">
                    <div class="card-header bg-light"><h5 class="mb-0">Log Aktivitas & Komentar</h5></div>
                    <div class="card-body d-flex flex-column">
                        <form action="{{ route('tickets.add_comment', $ticket->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-2">
                                <textarea name="komentar" class="form-control" rows="3" placeholder="Tulis komentar atau log aktivitas di sini..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm">Kirim Komentar</button>
                            </div>
                        </form>
                        <hr>
                        <div class="flex-grow-1" style="max-height: 300px; overflow-y: auto;">
                            @if ($ticket->logs->isEmpty())
                                <p class="text-muted text-center mt-3">Belum ada aktivitas.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($ticket->logs as $log)
                                        <li class="list-group-item px-0">
                                            <p class="mb-1">{{ nl2br(e($log->log_text)) }}</p>
                                            <small class="text-muted">oleh <strong>{{ $log->user }}</strong> pada {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Tiket</a>
        </div>

    </main>
    @endsection
    