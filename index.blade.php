<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Queue Display Board - Q-TRACK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #E93D5A; --dark: #1a1a1a; --gold: #FFD700; --blue: #60A5FA; --text-bright: #f1f5f9; --text-muted: #cbd5e1; }
        body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text-bright); margin: 0; min-height: 100vh; }
        .board-header {
            background: linear-gradient(135deg, #003366, #004080);
            border-bottom: 4px solid var(--primary);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .board-header .logo-text { font-size: 1.75rem; font-weight: 800; }
        .board-header .tagline { color: var(--primary); font-size: 1rem; }
        .board-header .time { font-size: 2rem; font-weight: 700; }
        .board-main { display: grid; grid-template-columns: 1fr 320px; gap: 0; min-height: calc(100vh - 180px); }
        @media (max-width: 992px) { .board-main { grid-template-columns: 1fr; } }
        .serving-section { padding: 40px; overflow: auto; background: #222; }
        .serving-title { font-size: 2.5rem; font-weight: 800; color: var(--primary); text-align: center; margin-bottom: 2rem; text-shadow: 0 0 20px rgba(233,61,90,0.3); }
        .serving-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 24px; }
        .serving-card {
            background: rgba(233,61,90,0.2);
            border: 3px solid var(--primary);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            animation: fadeIn 0.5s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .serving-card .number { font-size: 3.5rem; font-weight: 800; color: var(--gold); line-height: 1.2; text-shadow: 0 1px 4px rgba(0,0,0,0.5); }
        .serving-card .counter { font-size: 1.5rem; color: #93c5fd; font-weight: 700; margin-top: 8px; }
        .serving-card .service { font-size: 0.9rem; color: var(--text-bright); margin-top: 4px; font-weight: 500; }
        .serving-card.empty { border-color: #475569; background: rgba(30,41,59,0.8); }
        .serving-card.empty .number { color: #64748b; font-size: 2rem; }
        .serving-card.empty .counter { color: #93c5fd; }
        .serving-card.empty .service { color: var(--text-muted); }
        .board-stats-row { border-top: 2px solid #475569; }
        .board-stat-label { color: var(--text-muted); font-size: 1.1rem; }
        .board-stat-num { color: var(--gold); font-size: 1.5rem; }
        .board-sidebar {
            background: #1e293b;
            border-left: 3px solid var(--primary);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .sidebar-card { background: rgba(255,255,255,0.08); border: 1px solid #475569; border-radius: 12px; padding: 20px; }
        .sidebar-card h6 { color: var(--primary); font-weight: 700; margin-bottom: 12px; font-size: 1rem; }
        .sidebar-card .stat { font-size: 1.5rem; font-weight: 700; color: var(--text-bright); }
        .sidebar-card .small.text-muted { color: var(--text-muted) !important; }
        .board-footer {
            background: #0f172a;
            border-top: 3px solid var(--primary);
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .board-footer .text-muted { color: var(--text-muted) !important; }
        .board-footer .back-btn { color: #f87171; font-weight: 600; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>
    <header class="board-header">
        <div>
            <span class="logo-text">Q-TRACK</span>
            <span class="tagline d-block">Track your turn, Anytime</span>
        </div>
        <div class="text-center">
            <div>KWSP Kwasa Damansara</div>
            <div class="time" id="board-time">{{ now()->format('h:i A') }}</div>
        </div>
    </header>

    <div class="board-main">
        <section class="serving-section">
            <h2 class="serving-title">NOW SERVING</h2>
            <div class="serving-grid" id="board-content">
                @forelse($counters as $counter)
                <div class="serving-card empty" data-counter-id="{{ $counter->id }}">
                    <div class="number">-</div>
                    <div class="counter">Counter {{ $counter->number }}</div>
                    <div class="service">{{ $counter->service->name }}</div>
                </div>
                @empty
                <div class="col-12 text-center text-muted py-5">No counters configured.</div>
                @endforelse
            </div>
            <div class="mt-4 pt-4 board-stats-row">
                <div class="row text-center">
                    <div class="col-4"><span class="board-stat-label">Waiting: </span><strong class="board-stat-num" id="stat-waiting">0</strong></div>
                    <div class="col-4"><span class="board-stat-label">Served today: </span><strong class="board-stat-num" id="stat-completed">0</strong></div>
                </div>
            </div>
        </section>
        <aside class="board-sidebar">
            <div class="sidebar-card">
                <h6>ANNOUNCEMENTS</h6>
                <p class="small mb-0">Welcome to KWSP EPF. Please have your IC and documents ready. Service hours: Mon–Fri 9AM–5PM.</p>
            </div>
            <div class="sidebar-card">
                <h6>TODAY'S STATS</h6>
                <div class="stat" id="sidebar-waiting">0</div>
                <div class="small text-muted">Waiting</div>
                <div class="stat mt-2" id="sidebar-completed">0</div>
                <div class="small text-muted">Served today</div>
            </div>
        </aside>
    </div>

    <footer class="board-footer">
        <span class="text-muted small">Q-TRACK Queue Display Board</span>
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="back-btn">← Back to Admin Dashboard</a>
            @else
                <a href="{{ route('staff.dashboard') }}" class="back-btn">← Back to Staff Dashboard</a>
            @endif
        @else
            <a href="{{ route('home') }}" class="back-btn">← Back to Dashboard</a>
        @endauth
    </footer>

    <script>
        function refreshBoard() {
            fetch('{{ route("board.data") }}')
                .then(r => r.json())
                .then(data => {
                    data.called.forEach(item => {
                        const el = document.querySelector('.serving-card[data-counter-id="' + item.counter_id + '"]');
                        if (el) {
                            el.classList.remove('empty');
                            el.querySelector('.number').textContent = item.display_number;
                        }
                    });
                    document.querySelectorAll('.serving-card[data-counter-id]').forEach(el => {
                        const id = el.dataset.counterId;
                        if (!data.called.find(c => c.counter_id == id)) {
                            el.classList.add('empty');
                            el.querySelector('.number').textContent = '-';
                        }
                    });
                    if (document.getElementById('stat-waiting')) document.getElementById('stat-waiting').textContent = data.waiting || 0;
                    if (document.getElementById('stat-completed')) document.getElementById('stat-completed').textContent = data.completed_today || 0;
                    if (document.getElementById('sidebar-waiting')) document.getElementById('sidebar-waiting').textContent = data.waiting || 0;
                    if (document.getElementById('sidebar-completed')) document.getElementById('sidebar-completed').textContent = data.completed_today || 0;
                });
        }
        function updateTime() {
            const now = new Date();
            const el = document.getElementById('board-time');
            if (el) el.textContent = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        }
        refreshBoard();
        setInterval(refreshBoard, 3000);
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
