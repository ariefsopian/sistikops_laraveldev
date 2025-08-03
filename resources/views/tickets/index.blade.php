@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 text-dark">List of Tickets</h4>
    @if (Auth::check() && (Auth::user()->isRequester() || Auth::user()->isAdmin()))
        <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Tiket</a>
    @endif
</div>

<!-- Improved Filter Section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Options</h6>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>
    <div class="card-body collapse show" id="filterCollapse">
        <form action="{{ route('tickets.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- Date Range Filter -->
            <div class="col-lg-4 col-md-6">
                <label class="form-label fw-bold">Periode Waktu</label>
                <div class="input-group input-group-sm">
                    <input type="date" class="form-control" name="filter_tanggal_mulai" id="filter_tanggal_mulai"
                           value="{{ request('filter_tanggal_mulai') }}">
                    <span class="input-group-text">s/d</span>
                    <input type="date" class="form-control" name="filter_tanggal_akhir" id="filter_tanggal_akhir"
                           value="{{ request('filter_tanggal_akhir') }}">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="col-lg-2 col-md-6">
                <label for="filter_status" class="form-label fw-bold">Status</label>
                <select name="filter_status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['Open', 'In Progress', 'Pending Approval', 'Resolved', 'Closed'] as $s)
                    <option value="{{ $s }}"
                        {{ request('filter_status') == $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="col-lg-2 col-md-6">
                <label for="filter_priority" class="form-label fw-bold">Prioritas</label>
                <select name="filter_priority" class="form-select form-select-sm">
                    <option value="">Semua Prioritas</option>
                    @foreach(['Low', 'Medium', 'High'] as $p)
                    <option value="{{ $p }}"
                        {{ request('filter_priority') == $p ? 'selected' : '' }}>
                        {{ $p }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Requester Filter -->
            <div class="col-lg-4 col-md-6">
                <label for="filter_requester_select" class="form-label fw-bold">Requester</label>
                <select name="filter_requester" id="filter_requester_select" class="form-select form-select-sm">
                    <option value="">Semua Requester</option>
                    @foreach($allUsersForFilter as $user_filter)
                    <option value="{{ $user_filter->id }}"
                        {{ request('filter_requester') == $user_filter->id ? 'selected' : '' }}>
                        {{ $user_filter->username }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-undo me-1"></i> Reset
                </a>
                <a href="{{ route('tickets.export', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export
                </a>
            </div>
        </form>
    </div>
</div>

@if($tickets->total() > 0)
<div class="d-flex justify-content-end mb-2">
    <small class="text-muted">
        Menampilkan <strong>{{ $tickets->firstItem() }}-{{ $tickets->lastItem() }}</strong>
        dari <strong>{{ $tickets->total() }}</strong> tiket
    </small>
</div>
@endif

<div class="table-responsive shadow-sm bg-white rounded">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Indikator</th>
                <th>No. Tiket</th>
                <th>Requester</th>
                <th>Proyek</th>
                <th>Subjek</th>
                <th>Status</th>
                <th>Prioritas</th>
                <th>Jatuh Tempo</th>
                <th>Assignee</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
            <tr>
                <td>{{ $loop->iteration + ($tickets->currentPage() - 1) * $tickets->perPage() }}</td>
                <td class="text-center">
                    @php
                        $badge_class = 'bg-secondary';
                        $badge_text = 'N/A';
                        $is_active = !in_array($ticket->status, ['Resolved', 'Closed']);
                        $today = \Carbon\Carbon::now();

                        if ($is_active && !empty($ticket->due_date)) {
                            try {
                                $due_date = \Carbon\Carbon::parse($ticket->due_date);
                                if ($today->greaterThan($due_date)) {
                                    $badge_class = 'bg-danger';
                                    $badge_text = 'Overdue';
                                } elseif ($due_date->diffInDays($today, false) <= 2) {
                                    $badge_class = 'bg-warning text-dark';
                                    $badge_text = 'Due Soon';
                                } else {
                                    $badge_class = 'bg-success';
                                    $badge_text = 'On Track';
                                }
                            } catch (Exception $e) {
                                $badge_class = 'bg-dark';
                                $badge_text = 'Invalid Date';
                            }
                        } else {
                            $badge_class = 'bg-secondary';
                            $badge_text = 'Completed';
                        }
                    @endphp
                    <span class="badge rounded-pill {{ $badge_class }}">{{ $badge_text }}</span>
                </td>
                <td>
                    <a href="{{ route('tickets.show', $ticket->id) }}">{{ $ticket->ticket_no }}</a>
                    {{-- Tambahkan ikon "New" jika tiket dibuat hari ini --}}
                    @if ($ticket->created_at->isToday())
                        <span class="badge bg-success ms-1">NEW</span>
                    @endif
                </td>
                <td>{{ $ticket->requester->username ?? 'N/A' }}</td>
                <td>{{ $ticket->project ?? 'N/A' }}</td>
                <td>{{ $ticket->subject }}</td>
                <td>
                    <span class="badge rounded-pill {{ [
                        'Open'=>'bg-primary',
                        'In Progress'=>'bg-info text-dark',
                        'Pending Approval'=>'bg-warning text-dark',
                        'Resolved'=>'bg-success',
                        'Closed'=>'bg-secondary'
                    ][$ticket->status] ?? 'bg-light text-dark' }}">
                        {{ $ticket->status }}
                    </span>
                </td>
                <td>
                    <span class="badge rounded-pill {{ [
                        'High'=>'bg-danger',
                        'Medium'=>'bg-warning text-dark',
                        'Low'=>'bg-success'
                    ][$ticket->priority] ?? 'bg-light text-dark' }}">
                        {{ $ticket->priority }}
                    </span>
                </td>
                <td>{{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->format('d M Y') : 'N/A' }}</td>
                <td class="text-nowrap">{{ $ticket->assignee->username ?? 'N/A' }}</td>
                <td class="text-nowrap action-buttons">
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-outline-info btn-sm" title="Lihat"><i class="fas fa-eye"></i></a>
                    @if (Auth::user()->isAdmin() || $ticket->requester_id == Auth::id() || $ticket->assignee_id == Auth::id())
                    <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-outline-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-outline-danger btn-sm btn-delete-ticket" data-id="{{ $ticket->id }}" title="Hapus"><i class="fas fa-trash"></i></button>
                    @endif
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">Data tiket tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginasi --}}
{{ $tickets->appends(request()->query())->links('pagination::bootstrap-5') }}

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#filter_requester_select').select2({
        theme: "bootstrap-5",
        placeholder: "Cari Requester",
        allowClear: true
    });

    $('#filterCollapse').on('shown.bs.collapse', function() {
        $(this).prev('.card-header').find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }).on('hidden.bs.collapse', function() {
        $(this).prev('.card-header').find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });

    $('.btn-delete-ticket').on('click', function(e) {
        e.preventDefault();
        var ticketId = $(this).data('id');

        Swal.fire({
            title: 'Anda yakin?',
            text: "Data tiket ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('<form action="' + BASE_APP_PATH + 'tickets/' + ticketId + '" method="POST"></form>');
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
