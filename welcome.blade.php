@extends('layouts.app')

@section('title', 'Q-TRACK - Track your turn, Anytime')

@section('content')
{{-- Hero Section --}}
<div class="hero-section-custom">
    <div class="hero-overlay"></div>
    <div class="container py-5 position-relative">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 mb-4">
                {{-- Branch: static text only (boleh buang jika tak perlu) --}}
                <div class="branch-label-custom mb-4">
                    <i class="bi bi-geo-alt-fill me-2"></i>KWSP Kwasa Damansara
                </div>
                <h1 class="display-4 fw-bold mb-4 hero-title">
                    Track your turn,<br><span class="highlight-text">Anytime</span>
                </h1>
                <p class="lead mb-4 hero-desc">
                    Get your virtual number, track in real-time, and enjoy your visit with complete comfort and convenience.
                </p>
                <div class="d-flex gap-3 flex-wrap mb-4">
                    <a href="{{ route('visitor.services') }}" class="btn btn-primary-glow btn-lg">
                        <i class="bi bi-qr-code-scan me-2"></i>Get Queue Number
                    </a>
                    <a href="{{ route('board') }}" class="btn btn-outline-glass btn-lg">
                        <i class="bi bi-tv me-2"></i>View Display Board
                    </a>
                </div>
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-number" id="stat-current-wait">{{ $stats['current_wait'] }}</div>
                        <div class="stat-label">Current Wait</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="stat-active-counters">{{ $stats['active_counters'] }}</div>
                        <div class="stat-label">Active Counters</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">~<span id="stat-avg-time">{{ $stats['avg_service_time'] }}</span>m</div>
                        <div class="stat-label">Avg. Service Time</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="feature-card">
                    <div class="card-body text-center">
                        <div class="feature-icon mb-4"><i class="bi bi-phone"></i></div>
                        <h3 class="mb-4 feature-title">How It Works</h3>
                        <div class="row g-4">
                            <div class="col-4">
                                <div class="step-indicator">1</div>
                                <p class="step-text">Scan & Check In</p>
                            </div>
                            <div class="col-4">
                                <div class="step-indicator">2</div>
                                <p class="step-text">Get Virtual Number</p>
                            </div>
                            <div class="col-4">
                                <div class="step-indicator">3</div>
                                <p class="step-text">Track & Relax</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-top border-2 border-danger border-opacity-25">
                            <h5 class="mb-3 fw-bold" style="color: var(--secondary-color);">Available at KWSP Kwasa Damansara</h5>
                            <div class="row g-2">
                                @foreach($services as $service)
                                <div class="col-6">
                                    <button type="button" class="service-pill-btn w-100 service-btn" data-service="{{ json_encode([
                                        'id' => $service->id,
                                        'name' => $service->name,
                                        'description' => $service->description,
                                        'common_issues' => $service->common_issues,
                                        'estimated_time' => $service->estimated_time,
                                        'code' => $service->code,
                                    ]) }}">
                                        @if($service->code === 'WTH' || str_contains($service->name, 'Withdrawal'))<i class="bi bi-cash-stack me-1"></i>
                                        @elseif($service->code === 'NOM')<i class="bi bi-person-check me-1"></i>
                                        @elseif($service->code === 'CON')<i class="bi bi-file-text me-1"></i>
                                        @else<i class="bi bi-exclamation-triangle me-1"></i>
                                        @endif
                                        {{ Str::limit($service->name, 22) }}
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Available EPF Services Section --}}
<div class="bg-white py-5" style="border-top: 3px solid var(--primary-color);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-2 fw-bold" style="color: var(--secondary-color);">Available EPF Services</h2>
            <p class="text-muted">Click on any service to learn more about what we can help you with</p>
        </div>
        <div class="row g-4">
            @foreach($services as $service)
            <div class="col-md-6">
                <div class="card card-custom h-100 text-center service-card-interactive border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <div class="service-icon-main mb-3">
                            @if($service->code === 'WTH' || str_contains($service->name, 'Withdrawal'))<i class="bi bi-cash-stack"></i>
                            @elseif($service->code === 'NOM')<i class="bi bi-person-check"></i>
                            @elseif($service->code === 'CON')<i class="bi bi-file-text"></i>
                            @else<i class="bi bi-exclamation-triangle"></i>
                            @endif
                        </div>
                        <h5 class="card-title mb-3 fw-bold" style="color: var(--secondary-color);">{{ Str::limit($service->name, 50) }}</h5>
                        <p class="text-muted small mb-3">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="badge bg-danger rounded-pill px-3">{{ $service->code }}</span>
                            @if($service->estimated_time)<span class="text-muted small">Est. {{ $service->estimated_time }}</span>@endif
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm service-info-btn service-btn service-detail-btn" data-service="{{ json_encode([
                            'id' => $service->id,
                            'name' => $service->name,
                            'description' => $service->description,
                            'common_issues' => $service->common_issues,
                            'estimated_time' => $service->estimated_time,
                            'code' => $service->code,
                        ]) }}" data-modal-detail="1">
                            <i class="bi bi-info-circle me-1"></i>Learn More
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Service Info Modal --}}
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i><span id="modalServiceName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p id="modalServiceDesc" class="text-muted"></p>
                <div id="modalServiceExtra" class="service-extra-content"></div>
                <h6 class="fw-bold mt-3">Common issues we resolve</h6>
                <ul id="modalCommonIssues" class="text-muted small"></ul>
                <div class="mt-3"><span class="badge bg-primary rounded-pill px-3 py-2" id="modalEstTime"></span></div>
                <form id="modalBookForm" method="POST" action="{{ route('queue.store') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="service_id" id="modalServiceId">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Close</button>
                        <button type="submit" class="btn btn-danger fw-semibold"><i class="bi bi-plus-circle me-1"></i>Book This Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.hero-section-custom { position: relative; min-height: 75vh; display: flex; align-items: center; background: linear-gradient(135deg, #003366 0%, #004080 50%, rgba(196,30,58,0.3) 100%); }
.hero-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.35); }
.hero-section-custom .container { position: relative; z-index: 5; }
.branch-label-custom { color: rgba(255,255,255,0.95); font-weight: 600; font-size: 0.95rem; }
.hero-title { color: white; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); }
.highlight-text { color: #FFE4E8; text-shadow: 1px 1px 6px rgba(196,30,58,0.6); }
.hero-desc { color: rgba(255,255,255,0.95); font-size: 1.1rem; text-shadow: 1px 1px 4px rgba(0,0,0,0.4); }
.btn-primary-glow {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border: none; color: white; font-weight: 700; padding: 14px 28px; border-radius: 12px;
    box-shadow: 0 8px 25px rgba(196,30,58,0.4); transition: all 0.3s ease;
}
.btn-primary-glow:hover { color: white; transform: translateY(-2px); box-shadow: 0 12px 30px rgba(196,30,58,0.5); }
.btn-outline-glass {
    background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.4); color: white; font-weight: 600;
    padding: 12px 26px; border-radius: 12px; backdrop-filter: blur(10px); transition: all 0.3s ease;
}
.btn-outline-glass:hover { background: rgba(255,255,255,0.25); color: white; border-color: rgba(255,255,255,0.6); transform: translateY(-2px); }
.stats-container { display: flex; gap: 24px; flex-wrap: wrap; }
.stat-item {
    text-align: center; background: rgba(255,255,255,0.12); padding: 18px 22px; border-radius: 14px;
    backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); min-width: 110px;
}
.stat-number { font-size: 1.75rem; font-weight: 800; color: #FFE4E8; }
.stat-label { font-size: 0.85rem; color: rgba(255,255,255,0.85); margin-top: 4px; }
.feature-card {
    background: rgba(255,255,255,0.96); backdrop-filter: blur(16px); border-radius: 20px; padding: 28px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.4);
}
.feature-icon { font-size: 3.5rem; color: var(--primary-color); }
.feature-title { font-weight: 700; color: var(--secondary-color); }
.step-indicator {
    width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem;
    margin-bottom: 10px; box-shadow: 0 6px 18px rgba(196,30,58,0.3);
}
.step-text { font-weight: 600; color: var(--secondary-color); font-size: 0.85rem; }
.service-pill-btn {
    background: linear-gradient(135deg, rgba(196,30,58,0.1), rgba(196,30,58,0.05));
    color: var(--primary-color); padding: 8px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
    border: 1px solid rgba(196,30,58,0.25); transition: all 0.3s ease; cursor: pointer;
}
.service-pill-btn:hover { background: var(--primary-color); color: white; }
.service-card-interactive { cursor: pointer; transition: all 0.3s ease; }
.service-card-interactive:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(196,30,58,0.12); }
.service-icon-main {
    width: 64px; height: 64px; border-radius: 50%;
    background: linear-gradient(135deg, rgba(196,30,58,0.12), rgba(233,77,90,0.08));
    display: inline-flex; align-items: center; justify-content: center; font-size: 1.75rem; color: var(--primary-color);
}
.service-card-interactive:hover .service-icon-main { background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; }
.service-info-btn { border-color: var(--primary-color); color: var(--primary-color); }
.service-info-btn:hover { background: var(--primary-color); color: white; border-color: var(--primary-color); }
@media (max-width: 768px) {
    .hero-section-custom { min-height: auto; padding: 2rem 0; }
    .stats-container { justify-content: center; gap: 12px; }
    .stat-item { min-width: 90px; padding: 14px 18px; }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('serviceModal');
    const modalName = document.getElementById('modalServiceName');
    const modalDesc = document.getElementById('modalServiceDesc');
    const modalExtra = document.getElementById('modalServiceExtra');
    const modalIssues = document.getElementById('modalCommonIssues');
    const modalEst = document.getElementById('modalEstTime');
    const modalId = document.getElementById('modalServiceId');

    // Long content for bottom section "Learn More" only (by service code)
    const serviceDetailContent = {
        'CON': '<h6 class="text-primary mb-2 mt-3"><i class="bi bi-list-ul me-2"></i>What we can help with</h6><ul class="list-unstyled small mb-0"><li class="mb-1"><i class="bi bi-calendar-month me-2 text-success"></i><strong>Check monthly contributions</strong> – verify records from employers</li><li class="mb-1"><i class="bi bi-calculator me-2 text-info"></i><strong>Verify contribution amounts</strong> – confirm correct calculations</li><li class="mb-1"><i class="bi bi-printer me-2 text-warning"></i><strong>Print EPF statements</strong> – for loans, visa or official use</li><li class="mb-1"><i class="bi bi-exclamation-triangle me-2 text-danger"></i><strong>Resolve missing records</strong> – clarify incorrect or missing contributions</li></ul><div class="alert alert-light border mt-3 small"><strong>Tip:</strong> We can print official EPF statements on the spot for urgent needs.</div>',
        'WTH': '<h6 class="text-primary mb-2 mt-3"><i class="bi bi-list-ul me-2"></i>What we can help with</h6><ul class="list-unstyled small mb-0"><li class="mb-1"><i class="bi bi-house me-2 text-success"></i><strong>Housing withdrawal</strong> – purchase, construction or housing loan</li><li class="mb-1"><i class="bi bi-mortarboard me-2 text-info"></i><strong>Education withdrawal</strong> – for self or children\'s education</li><li class="mb-1"><i class="bi bi-heart-pulse me-2 text-danger"></i><strong>Medical withdrawal</strong> – critical illness or treatment</li><li class="mb-1"><i class="bi bi-geo-alt me-2 text-warning"></i><strong>Hajj withdrawal</strong> – pilgrimage expenses</li><li class="mb-1"><i class="bi bi-calendar-check me-2 text-secondary"></i><strong>Age-based withdrawals</strong> – Age 50, 55 or full retirement</li><li class="mb-1"><i class="bi bi-fingerprint me-2 text-primary"></i><strong>Application assistance</strong> – thumbprint verification and submission help</li></ul><div class="alert alert-light border mt-3 small"><strong>Common questions:</strong> Eligibility, document requirements, application status, officer-assisted submission.</div>',
        'NOM': '<h6 class="text-primary mb-2 mt-3"><i class="bi bi-list-ul me-2"></i>What we can help with</h6><ul class="list-unstyled small mb-0"><li class="mb-1"><i class="bi bi-person-plus me-2 text-success"></i><strong>Register new nomination</strong> – complete EPF Form 4 (first-time)</li><li class="mb-1"><i class="bi bi-person-gear me-2 text-info"></i><strong>Update nomination</strong> – change beneficiaries or details</li><li class="mb-1"><i class="bi bi-person-x me-2 text-warning"></i><strong>Cancel nomination</strong> – remove existing nomination</li><li class="mb-1"><i class="bi bi-shield-check me-2 text-primary"></i><strong>Identity verification</strong> – physical presence required for confirmation</li></ul><div class="alert alert-info mt-3 small"><i class="bi bi-info-circle me-2"></i><strong>Important:</strong> This service requires physical presence at the counter. Please bring your IC and relevant documents.</div>',
        'EMP': '<h6 class="text-primary mb-2 mt-3"><i class="bi bi-list-ul me-2"></i>What we can help with</h6><ul class="list-unstyled small mb-0"><li class="mb-1"><i class="bi bi-file-earmark-text me-2 text-danger"></i><strong>Lodge complaints</strong> – against non-compliant employers</li><li class="mb-1"><i class="bi bi-clock-history me-2 text-warning"></i><strong>Report late or unpaid contributions</strong></li><li class="mb-1"><i class="bi bi-info-circle me-2 text-info"></i><strong>Seek advice</strong> – on EPF contribution violation procedures</li><li class="mb-1"><i class="bi bi-shield-check me-2 text-success"></i><strong>Know your rights</strong> – employee rights on EPF contributions</li></ul><div class="alert alert-light border mt-3 small"><strong>We handle:</strong> Employer deducts but does not remit EPF; delayed or incorrect contributions; how to make an official complaint.</div>',
        'CMP': '<h6 class="text-primary mb-2 mt-3"><i class="bi bi-list-ul me-2"></i>What we can help with</h6><ul class="list-unstyled small mb-0"><li class="mb-1"><i class="bi bi-file-earmark-text me-2 text-danger"></i><strong>Lodge complaints</strong> – against non-compliant employers</li><li class="mb-1"><i class="bi bi-clock-history me-2 text-warning"></i><strong>Report late or unpaid contributions</strong></li><li class="mb-1"><i class="bi bi-info-circle me-2 text-info"></i><strong>Seek advice</strong> – on EPF contribution violation procedures</li><li class="mb-1"><i class="bi bi-shield-check me-2 text-success"></i><strong>Know your rights</strong> – employee rights on EPF contributions</li></ul><div class="alert alert-light border mt-3 small"><strong>We handle:</strong> Employer deducts but does not remit EPF; delayed or incorrect contributions; how to make an official complaint.</div>'
    };

    document.querySelectorAll('.service-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var s = JSON.parse(this.getAttribute('data-service'));
            var showDetail = this.getAttribute('data-modal-detail') === '1';
            modalName.textContent = s.name;
            modalDesc.textContent = s.description || '—';
            var code = (s.code || '').toUpperCase();
            modalExtra.innerHTML = showDetail && serviceDetailContent[code] ? serviceDetailContent[code] : '';
            var issues = (s.common_issues || '—').split(/[;,]|\n/).filter(Boolean).map(function(i) { return '<li>' + i.trim() + '</li>'; }).join('');
            modalIssues.innerHTML = issues || '<li>—</li>';
            modalEst.textContent = s.estimated_time ? 'Estimated: ' + s.estimated_time : '—';
            modalId.value = s.id;
            new bootstrap.Modal(modal).show();
        });
    });

    function refreshStats() {
        fetch('{{ route("visitor.dashboard.stats") }}').then(function(r) { return r.json(); }).then(function(d) {
            var el = document.getElementById('stat-current-wait'); if (el) el.textContent = d.current_wait;
            el = document.getElementById('stat-active-counters'); if (el) el.textContent = d.active_counters;
            el = document.getElementById('stat-avg-time'); if (el) el.textContent = d.avg_service_time;
        });
    }
    setInterval(refreshStats, 15000);
})();
</script>
@endpush
@endsection
