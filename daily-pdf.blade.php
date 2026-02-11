<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .report-header { margin-bottom: 12px; }
        .report-logos { margin-bottom: 8px; }
        .report-logos img { height: 52px; width: auto; max-width: 120px; display: block; vertical-align: middle; }
        .report-logos .logo-sep { width: 2px; height: 44px; background: #E93D5A; margin: 0; }
        .report-logos td { border: none; padding: 0 6px 0 0; vertical-align: middle; }
        .report-logos td.logo-sep-cell { padding: 0 6px; width: 1px; }
        .report-logos td:last-child { padding-right: 0; }
        h1 { color: #003366; font-size: 18px; border-bottom: 2px solid #E93D5A; padding-bottom: 8px; margin-top: 8px; }
        h2 { font-size: 14px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #003366; color: white; }
        .stats { margin: 15px 0; }
        .stats span { display: inline-block; margin-right: 20px; padding: 8px 12px; background: #f5f5f5; }
        .charts-row { margin: 18px 0; }
        .chart-box { margin-bottom: 15px; }
        .chart-box h3 { font-size: 12px; color: #003366; margin-bottom: 6px; }
        .footer { margin-top: 30px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="report-header">
        <table class="report-logos" style="border: none; width: auto;"><tr style="border: none;">
            @if(file_exists(public_path('images/qtrack-logo.png')))
                <td><img src="{{ public_path('images/qtrack-logo.png') }}" alt="Q-TRACK"></td>
            @endif
            @if(file_exists(public_path('images/qtrack-logo.png')) && file_exists(public_path('images/kwsp-logo.png')))
                <td class="logo-sep-cell"><div class="logo-sep"></div></td>
            @endif
            @if(file_exists(public_path('images/kwsp-logo.png')))
                <td><img src="{{ public_path('images/kwsp-logo.png') }}" alt="KWSP EPF"></td>
            @endif
        </tr></table>
        <h1>Q-TRACK Daily Queue Report</h1>
        <p><strong>Date:</strong> {{ $date->format('d M Y') }} | <strong>Branch:</strong> KWSP Kwasa Damansara</p>
    </div>

    <div class="stats">
        <strong>Summary:</strong>
        <span>Total: {{ $stats['total'] }}</span>
        <span>Waiting: {{ $stats['waiting'] }}</span>
        <span>Called: {{ $stats['called'] }}</span>
        <span>Completed: {{ $stats['completed'] }}</span>
        <span>Skipped: {{ $stats['skipped'] }}</span>
    </div>

    {{-- Graphs (HTML/CSS so DomPDF renders them) --}}
    @php
        $totalStat = $stats['total'] ?: 1;
        $pctW = round(100 * $stats['waiting'] / $totalStat, 1);
        $pctC = round(100 * $stats['called'] / $totalStat, 1);
        $pctD = round(100 * $stats['completed'] / $totalStat, 1);
        $pctS = round(100 * $stats['skipped'] / $totalStat, 1);
        $maxBar = $byService->isEmpty() ? 1 : max(1, $byService->max('total'));
    @endphp
    <table class="charts-row" style="border: none; width: 100%;"><tr style="border: none;"><td style="border: none; vertical-align: top; width: 260px;">
        <div class="chart-box">
            <h3>Queue Status Today</h3>
            @if($stats['total'] == 0)
                <table style="width: 200px; border: 1px solid #ddd;"><tr><td style="height: 60px; text-align: center; background: #f5f5f5; color: #666;">No data</td></tr></table>
            @else
                <table style="width: 200px; height: 24px; border-collapse: collapse; border: none;">
                    <tr style="border: none;">
                        <td style="width: {{ $pctW }}%; height: 24px; background: #fbbf24; border: none; padding: 0;"></td>
                        <td style="width: {{ $pctC }}%; height: 24px; background: #60a5fa; border: none; padding: 0;"></td>
                        <td style="width: {{ $pctD }}%; height: 24px; background: #34d399; border: none; padding: 0;"></td>
                        <td style="width: {{ $pctS }}%; height: 24px; background: #f87171; border: none; padding: 0;"></td>
                    </tr>
                </table>
                <p style="margin: 4px 0 0 0; font-size: 10px;"><strong>Total: {{ $stats['total'] }}</strong></p>
            @endif
            <table style="font-size: 9px; margin-top: 8px; width: 180px;">
                <tr><td style="border: none; padding: 2px 6px 2px 0; width: 14px;"><span style="display: inline-block; width: 10px; height: 10px; background: #fbbf24;"></span></td><td style="border: none; padding: 2px 0;">Waiting {{ $stats['waiting'] }}</td></tr>
                <tr><td style="border: none; padding: 2px 6px 2px 0;"><span style="display: inline-block; width: 10px; height: 10px; background: #60a5fa;"></span></td><td style="border: none; padding: 2px 0;">Called {{ $stats['called'] }}</td></tr>
                <tr><td style="border: none; padding: 2px 6px 2px 0;"><span style="display: inline-block; width: 10px; height: 10px; background: #34d399;"></span></td><td style="border: none; padding: 2px 0;">Completed {{ $stats['completed'] }}</td></tr>
                <tr><td style="border: none; padding: 2px 6px 2px 0;"><span style="display: inline-block; width: 10px; height: 10px; background: #f87171;"></span></td><td style="border: none; padding: 2px 0;">Skipped {{ $stats['skipped'] }}</td></tr>
            </table>
        </div>
        </td><td style="border: none; vertical-align: top;">
        <div class="chart-box">
            <h3>By Service (Total)</h3>
            @if($byService->isEmpty())
                <table style="width: 100%; border: 1px solid #ddd;"><tr><td style="height: 60px; text-align: center; background: #f5f5f5; color: #666;">No service data</td></tr></table>
            @else
                <table style="width: 100%; border: none; font-size: 10px;">
                    @foreach($byService as $row)
                        @php $barPct = $maxBar > 0 ? min(100, round(100 * $row['total'] / $maxBar)) : 0; @endphp
                        <tr style="border: none;">
                            <td style="border: none; padding: 2px 8px 2px 0; width: 45%; vertical-align: middle;">{{ Str::limit($row['name'], 35) }}</td>
                            <td style="border: none; padding: 2px 0; width: 45%; vertical-align: middle;">
                                <table style="width: 100%; height: 14px; border-collapse: collapse; border: none;"><tr style="border: none;">
                                    <td style="width: {{ $barPct }}%; height: 14px; background: #003366; border: none;"></td>
                                    <td style="width: {{ 100 - $barPct }}%; height: 14px; background: #f0f0f0; border: none;"></td>
                                </tr></table>
                            </td>
                            <td style="border: none; padding: 2px 0 2px 6px; font-weight: bold; width: 10%;">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
        </td></tr></table>

    <h2>By Service</h2>
    <table>
        <thead><tr><th>Service</th><th>Total</th><th>Completed</th></tr></thead>
        <tbody>
            @foreach($byService as $row)
            <tr><td>{{ $row['name'] }}</td><td>{{ $row['total'] }}</td><td>{{ $row['completed'] }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Recent Queue Tickets (first 100)</h2>
    <table>
        <thead><tr><th>#</th><th>Queue No</th><th>Service</th><th>Status</th><th>Counter</th></tr></thead>
        <tbody>
            @foreach($queues as $q)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $q->display_number }}</td>
                <td>{{ $q->service?->name ?? '-' }}</td>
                <td>{{ ucfirst($q->status) }}</td>
                <td>{{ $q->counter?->number ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i') }} | Q-TRACK - Track your turn, Anytime | KWSP EPF
    </div>
</body>
</html>
