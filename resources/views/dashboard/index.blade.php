@extends('layouts.app') {{-- Mewarisi dari layout utama --}}

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 h-100 shadow-sm">
            <div class="card-body"><div class="row align-items-center"><div class="col me-2"><div class="text-xs fw-bold text-primary text-uppercase mb-1">Tiket Open</div><div class="h5 mb-0 fw-bold text-gray-800">{{ $total_open }}</div></div><div class="col-auto"><i class="fas fa-folder-open fa-2x text-gray-300"></i></div></div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4 h-100 shadow-sm">
            <div class="card-body"><div class="row align-items-center"><div class="col me-2"><div class="text-xs fw-bold text-info text-uppercase mb-1">In Progress</div><div class="h5 mb-0 fw-bold text-gray-800">{{ $total_progress }}</div></div><div class="col-auto"><i class="fas fa-spinner fa-2x text-gray-300"></i></div></div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 h-100 shadow-sm">
            <div class="card-body"><div class="row align-items-center"><div class="col me-2"><div class="text-xs fw-bold text-warning text-uppercase mb-1">Jatuh Tempo Hari Ini</div><div class="h5 mb-0 fw-bold text-gray-800">{{ $total_due_today }}</div></div><div class="col-auto"><i class="fas fa-calendar-day fa-2x text-gray-300"></i></div></div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 h-100 shadow-sm">
                 <div class="card-body"><div class="row align-items-center"><div class="col me-2"><div class="text-xs fw-bold text-success text-uppercase mb-1">Ditugaskan ke Saya</div><div class="h5 mb-0 fw-bold text-gray-800">{{ $total_my_tickets }}</div></div><div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div></div></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-7 col-lg-7">
        <div class="card shadow-sm mb-4"><div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Tiket Dibuat per Bulan</h6></div><div class="card-body"><canvas id="monthlyChart"></canvas></div></div>
    </div>
    <div class="col-xl-5 col-lg-5">
        <div class="card shadow-sm mb-4"><div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Tiket Berdasarkan Status</h6></div><div class="card-body"><canvas id="statusChart"></canvas></div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-danger text-white"><h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Tiket Prioritas (Terlambat & Akan Datang)</h6></div>
            <div class="card-body">
                @if ($priority_tickets->isEmpty())
                    <p class="text-muted text-center my-4">Tidak ada tiket yang terlambat atau akan jatuh tempo.</p>
                @else
                    <div class="list-group list-group-flush">
                    @foreach ($priority_tickets as $ticket)
                        @php
                            $is_overdue = \Carbon\Carbon::parse($ticket->due_date)->isPast() && !$ticket->status == 'Resolved' && !$ticket->status == 'Closed';
                        @endphp
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><strong>#{{ $ticket->ticket_no }}:</strong> {{ $ticket->subject }}</div>
                            <span class="badge {{ $is_overdue ? 'bg-danger' : 'bg-warning text-dark' }}">{{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y') }}</span>
                        </a>
                    @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
         <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white"><h6 class="mb-0 fw-bold"><i class="fas fa-user-check me-2"></i>Tiket yang Ditugaskan ke Saya</h6></div>
            <div class="card-body">
                 @if ($my_assigned_tickets->isEmpty())
                    <p class="text-muted text-center my-4">Tidak ada tiket aktif yang ditugaskan kepada Anda.</p>
                @else
                    <div class="list-group list-group-flush">
                    @foreach ($my_assigned_tickets as $ticket)
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="list-group-item list-group-item-action">
                            <strong>#{{ $ticket->ticket_no }}:</strong> {{ $ticket->subject }}
                            <small class="text-muted float-end">dari {{ $ticket->requester->username ?? 'N/A' }}</small>
                        </a>
                    @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusLabels = @json($chart_status_labels);
    const statusData = @json($chart_status_data);
    const statusColors = @json($chart_status_colors);
    // PERBAIKI BARIS INI: Gunakan monthly_labels dan monthly_values yang dikirim dari controller
    const monthlyLabels = @json($monthly_labels);
    const monthlyData = @json($monthly_values); // Menggunakan monthly_values

    new Chart(document.getElementById('statusChart'),{type:'pie',data:{labels:statusLabels,datasets:[{data:statusData,backgroundColor:statusColors,hoverOffset:4}]},options:{responsive:!0,maintainAspectRatio:!1,plugins:{legend:{position:'bottom'}}}});
    // PERBAIKI BARIS INI: Gunakan monthlyLabels dan monthlyData yang sudah benar
    new Chart(document.getElementById('monthlyChart'),{type:'bar',data:{labels:monthlyLabels,datasets:[{label:'Jumlah Tiket',data:monthlyData,backgroundColor:'rgba(13, 110, 253, 0.5)',borderColor:'rgba(13, 110, 253, 1)',borderWidth:1}]},options:{scales:{y:{beginAtZero:!0,ticks:{stepSize:1}}},responsive:!0,maintainAspectRatio:!1}});
</script>
@endpush
