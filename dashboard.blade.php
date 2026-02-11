@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Today's Queue</p>
                        <h3 class="mb-0 fw-bold" id="kpi-today">{{ $stats['today_queue_count'] }}</h3>
                        <small class="text-success" id="kpi-today-trend">@if($stats['today_queue_trend'] >= 0)↗ @endif {{ $stats['today_queue_trend'] }}% today</small>
                    </div>
                    <div class="rounded-3 p-2" style="background: rgba(233,61,90,0.12);"><i class="bi bi-list-ol fs-4" style="color: #E93D5A;"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Visitors (Today)</p>
                        <h3 class="mb-0 fw-bold" id="kpi-visitors">{{ $stats['total_visitors'] }}</h3>
                        <small class="text-success" id="kpi-visitors-trend">@if($stats['visitors_week_trend'] >= 0)↗ @endif {{ $stats['visitors_week_trend'] }}% vs last week</small>
                    </div>
                    <div class="rounded-3 p-2" style="background: rgba(34,197,94,0.12);"><i class="bi bi-people fs-4 text-success"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Avg. Service Time</p>
                        <h3 class="mb-0 fw-bold" id="kpi-wait">{{ $stats['avg_wait_mins'] }}<span class="fs-6 fw-normal text-muted">m</span></h3>
                        <small class="text-muted">completed tickets</small>
                    </div>
                    <div class="rounded-3 p-2" style="background: rgba(96,165,250,0.12);"><i class="bi bi-clock fs-4 text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Completed Today</p>
                        <h3 class="mb-0 fw-bold" id="kpi-completed">{{ $stats['completed_today'] }}</h3>
                        <small class="text-success" id="kpi-rate">{{ $stats['completion_rate'] }}% rate</small>
                    </div>
                    <div class="rounded-3 p-2" style="background: rgba(34,197,94,0.12);"><i class="bi bi-check2-circle fs-4 text-success"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Staff Status (Active / Non-active from staff toggle) --}}
@if(isset($staffStatus) && $staffStatus->isNotEmpty())
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-dashboard border-0">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h5 class="fw-bold mb-0">Staff Status</h5>
            </div>
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @foreach($staffStatus as $s)
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold">{{ $s->name }}</span>
                        <span class="badge {{ ($s->is_active ?? true) ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ ($s->is_active ?? true) ? 'Active' : 'Non-active' }}</span>
                        @if($s->role === 'admin')<span class="badge bg-dark rounded-pill">Admin</span>@endif
                        @if($s->counters->isNotEmpty())
                            <span class="text-muted small">Counter {{ $s->counters->sortBy('number')->pluck('number')->join(', ') }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Row 1: Line Chart + Doughnut --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card card-dashboard border-0">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h5 class="fw-bold mb-0">Queue Trends (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="chartLine" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-dashboard border-0">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h5 class="fw-bold mb-0">Queue Status Today</h5>
            </div>
            <div class="card-body d-flex justify-content-center">
                <canvas id="chartDoughnut" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Row 2: Bar Chart --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card card-dashboard border-0">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h5 class="fw-bold mb-0">Service Type Distribution (Today)</h5>
            </div>
            <div class="card-body">
                <canvas id="chartBar" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Row 3: Real-time Queue Table --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.reports.pdf') }}" class="btn btn-sm text-white" style="background: #003366;" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Download Daily Report (PDF)</a>
        @if(request()->has('date'))<a href="{{ route('admin.reports.pdf', ['date' => request('date')]) }}" class="btn btn-sm btn-outline-secondary ms-2" target="_blank">Custom date</a>@endif
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-dashboard border-0">
            <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0">Current Active Queues</h5>
                <small class="text-muted"><i class="bi bi-arrow-clockwise me-1"></i>Auto-refresh every 5s</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Queue #</th>
                                <th>Service</th>
                                <th>Wait / Serving</th>
                                <th>Status</th>
                                <th>Counter</th>
                                <th>Staff</th>
                                <th>Assign</th>
                            </tr>
                        </thead>
                        <tbody id="active-queues-tbody">
                            @forelse($activeQueues as $q)
                            <tr>
                                <td><strong>{{ $q['display_number'] }}</strong></td>
                                <td>{{ $q['service'] }}</td>
                                <td>@if($q['wait_mins'] !== null){{ $q['wait_mins'] }}m @else - @endif</td>
                                <td><span class="badge status-badge status-{{ $q['status'] }}">{{ ucfirst($q['status']) }}</span></td>
                                <td>{{ $q['counter'] }}</td>
                                <td>@if(!empty($q['staff_name'])){{ $q['staff_name'] }} <span class="text-muted">({{ $q['staff_id'] ?? '-' }})</span>@else—@endif</td>
                                <td>
                                    @if($q['status'] === 'waiting' && isset($countersForAssign[$q['service_id']]))
                                        <button type="button" class="btn btn-sm btn-outline-primary assign-counter-btn" data-queue-id="{{ $q['id'] }}" data-service-id="{{ $q['service_id'] }}" data-display-number="{{ $q['display_number'] }}">Assign</button>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No active queues</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Assign counter to queue --}}
<div class="modal fade" id="assignCounterModal" tabindex="-1" aria-labelledby="assignCounterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.queue.assign') }}" id="assign-counter-form">
                @csrf
                <input type="hidden" name="queue_id" id="assign-queue-id">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignCounterModalLabel">Assign Counter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Queue: <strong id="assign-display-number"></strong></p>
                    <div class="mb-3">
                        <label for="assign-counter-id" class="form-label">Counter</label>
                        <select name="counter_id" id="assign-counter-id" class="form-select" required>
                            <option value="">Select counter…</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background: #E93D5A;">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    const primaryColor = '#E93D5A';
    const primaryRgba = 'rgba(233, 61, 90, 0.1)';
    const statusColors = { waiting: '#fbbf24', called: '#60a5fa', completed: '#34d399', skipped: '#f87171' };

    // Line Chart - Queue Trends (no animation so it doesn't "move")
    new Chart(document.getElementById('chartLine'), {
        type: 'line',
        data: {
            labels: @json($chartLine['labels']),
            datasets: [{
                label: 'Queue Count',
                data: @json($chartLine['data']),
                borderColor: primaryColor,
                backgroundColor: primaryRgba,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Doughnut - Status (use placeholder when all zeros so chart renders)
    var doughnutData = @json($chartDoughnut['data']);
    var doughnutLabels = @json($chartDoughnut['labels']);
    var doughnutTotal = doughnutData.reduce(function(a, b) { return a + b; }, 0);
    if (doughnutTotal === 0) {
        doughnutLabels = ['No activity today'];
        doughnutData = [1];
    }
    new Chart(document.getElementById('chartDoughnut'), {
        type: 'doughnut',
        data: {
            labels: doughnutLabels,
            datasets: [{
                data: doughnutData,
                backgroundColor: doughnutTotal === 0 ? ['#e5e7eb'] : ['#fbbf24', '#60a5fa', '#34d399', '#f87171'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Bar Chart - Service Distribution
    const barLabels = @json($chartBar['labels']);
    const barData = @json($chartBar['data']);
    const barColors = barData.map(function(_, i) {
        const a = 0.3 + (0.6 * (1 - i / Math.max(barData.length, 1)));
        return 'rgba(233, 61, 90, ' + a + ')';
    });
    new Chart(document.getElementById('chartBar'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Queue Count',
                data: barData,
                backgroundColor: barColors,
                borderColor: primaryColor,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    var countersForAssign = @json($countersForAssign ?? []);
    // Real-time table refresh
    function refreshDashboard() {
        fetch('{{ route("admin.dashboard.data") }}')
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.stats) {
                    document.getElementById('kpi-today').textContent = res.stats.today_queue_count;
                    document.getElementById('kpi-today-trend').textContent = (res.stats.today_queue_trend >= 0 ? '↗ ' : '') + res.stats.today_queue_trend + '% today';
                    document.getElementById('kpi-visitors').textContent = res.stats.total_visitors;
                    document.getElementById('kpi-visitors-trend').textContent = (res.stats.visitors_week_trend >= 0 ? '↗ ' : '') + res.stats.visitors_week_trend + '% vs last week';
                    document.getElementById('kpi-wait').innerHTML = res.stats.avg_wait_mins + '<span class="fs-6 fw-normal text-muted">m</span>';
                    document.getElementById('kpi-completed').textContent = res.stats.completed_today;
                    document.getElementById('kpi-rate').textContent = res.stats.completion_rate + '% rate';
                }
                if (res.active_queues) {
                    var tbody = document.getElementById('active-queues-tbody');
                    if (res.active_queues.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No active queues</td></tr>';
                    } else {
                        tbody.innerHTML = res.active_queues.map(function(q) {
                            var wait = q.wait_mins != null ? q.wait_mins + 'm' : '-';
                            var staffText = (q.staff_name && q.staff_name.length) ? q.staff_name + ' <span class="text-muted">(' + (q.staff_id || '-') + ')</span>' : '—';
                            var assignCell = q.status !== 'waiting' || !countersForAssign[q.service_id] || !countersForAssign[q.service_id].length ? '<td>—</td>' : '<td><button type="button" class="btn btn-sm btn-outline-primary assign-counter-btn" data-queue-id="' + q.id + '" data-service-id="' + q.service_id + '" data-display-number="' + q.display_number + '">Assign</button></td>';
                            return '<tr><td><strong>' + q.display_number + '</strong></td><td>' + q.service + '</td><td>' + wait + '</td><td><span class="badge status-badge status-' + q.status + '">' + q.status.charAt(0).toUpperCase() + q.status.slice(1) + '</span></td><td>' + q.counter + '</td><td>' + staffText + '</td>' + assignCell + '</tr>';
                        }).join('');
                        document.querySelectorAll('#active-queues-tbody .assign-counter-btn').forEach(function(btn) {
                            btn.onclick = function() {
                                var queueId = this.getAttribute('data-queue-id');
                                var serviceId = this.getAttribute('data-service-id');
                                var displayNumber = this.getAttribute('data-display-number');
                                document.getElementById('assign-queue-id').value = queueId;
                                document.getElementById('assign-display-number').textContent = displayNumber;
                                var sel = document.getElementById('assign-counter-id');
                                sel.innerHTML = '<option value="">Select counter…</option>';
                                (countersForAssign[serviceId] || []).forEach(function(c) {
                                    var opt = document.createElement('option');
                                    opt.value = c.id;
                                    opt.textContent = 'Counter ' + c.number;
                                    sel.appendChild(opt);
                                });
                                new bootstrap.Modal(document.getElementById('assignCounterModal')).show();
                            };
                        });
                    }
                }
            });
    }

    document.querySelectorAll('.assign-counter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var queueId = this.getAttribute('data-queue-id');
            var serviceId = this.getAttribute('data-service-id');
            var displayNumber = this.getAttribute('data-display-number');
            document.getElementById('assign-queue-id').value = queueId;
            document.getElementById('assign-display-number').textContent = displayNumber;
            var sel = document.getElementById('assign-counter-id');
            sel.innerHTML = '<option value="">Select counter…</option>';
            (countersForAssign[serviceId] || []).forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = 'Counter ' + c.number;
                sel.appendChild(opt);
            });
            new bootstrap.Modal(document.getElementById('assignCounterModal')).show();
        });
    });

    setInterval(refreshDashboard, 5000);
})();
</script>
@endpush

@push('styles')
<style>
.status-badge { font-weight: 600; }
.status-waiting { background: #fbbf24; color: #1f2937; }
.status-called { background: #60a5fa; color: #fff; }
.status-completed { background: #34d399; color: #1f2937; }
.status-skipped { background: #f87171; color: #fff; }
</style>
@endpush
@endsection
