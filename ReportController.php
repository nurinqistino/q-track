<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Calendar-based reports dashboard.
     */
    public function index(Request $request): View
    {
        $now = now();
        $monthInput = $request->input('month', $now->format('Y-m'));
        $dateInput = $request->input('date', $now->format('Y-m-d'));

        try {
            $start = Carbon::parse($monthInput . '-01')->startOfMonth();
        } catch (\Throwable) {
            $start = $now->copy()->startOfMonth();
        }
        $end = $start->copy()->endOfMonth();
        $month = $start->format('Y-m');
        $daysInMonth = (int) $end->format('d');
        $firstWeekday = (int) $start->dayOfWeek; // 0 = Sunday

        $rawCounts = DB::table('queue_tickets')
            ->whereBetween('ticket_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->selectRaw('ticket_date, count(*) as total')
            ->groupBy('ticket_date')
            ->pluck('total', 'ticket_date');

        $dailyCounts = [];
        foreach ($rawCounts as $dateKey => $total) {
            $dateStr = $dateKey instanceof \DateTimeInterface
                ? \Carbon\Carbon::parse($dateKey)->format('Y-m-d')
                : (string) $dateKey;
            $dailyCounts[$dateStr] = (int) $total;
        }

        try {
            $date = Carbon::parse($dateInput)->startOfDay();
            $selectedDate = $date->format('Y-m-d');
        } catch (\Throwable) {
            $date = $now->copy()->startOfDay();
            $selectedDate = $date->format('Y-m-d');
        }
        $dayStats = $this->getDailyStats($date);

        $prevMonth = $start->copy()->subMonth()->format('Y-m');
        $nextMonth = $start->copy()->addMonth()->format('Y-m');

        return view('admin.reports.index', compact(
            'month',
            'start',
            'daysInMonth',
            'firstWeekday',
            'dailyCounts',
            'selectedDate',
            'date',
            'dayStats',
            'prevMonth',
            'nextMonth'
        ));
    }

    /**
     * Generate daily queue report as PDF.
     */
    public function pdf(Request $request)
    {
        try {
            $date = $request->has('date')
                ? Carbon::parse($request->date)->startOfDay()
                : Carbon::today();
        } catch (\Throwable) {
            $date = Carbon::today();
        }

        $queues = Queue::whereDate('ticket_date', $date)
            ->with('service', 'counter')
            ->orderBy('sequence')
            ->get();

        $byStatus = $queues->groupBy('status')->map->count();
        $byService = $queues->groupBy('service_id')->map(function ($items) {
            return [
                'name' => $items->first()->service?->name ?? 'N/A',
                'total' => $items->count(),
                'completed' => $items->where('status', 'completed')->count(),
            ];
        })->values();

        $stats = [
            'total' => $queues->count(),
            'waiting' => $byStatus->get('waiting', 0),
            'called' => $byStatus->get('called', 0),
            'completed' => $byStatus->get('completed', 0),
            'skipped' => $byStatus->get('skipped', 0),
        ];

        $html = view('admin.reports.daily-pdf', [
            'date' => $date,
            'stats' => $stats,
            'byService' => $byService,
            'queues' => $queues->take(100),
        ])->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->setOption('dpi', 120);

        return $pdf->download('q-track-report-' . $date->format('Y-m-d') . '.pdf');
    }

    private function getDailyStats(Carbon $date): array
    {
        $queues = Queue::whereDate('ticket_date', $date)->with('service')->get();
        $total = $queues->count();
        $completed = $queues->where('status', 'completed')->count();
        $skipped = $queues->where('status', 'skipped')->count();
        $waiting = $queues->where('status', 'waiting')->count();
        $called = $queues->where('status', 'called')->count();

        $avgMins = 0;
        $withTime = $queues->where('status', 'completed')->filter(fn ($q) => $q->called_at && $q->completed_at);
        if ($withTime->isNotEmpty()) {
            $avgMins = (int) round($withTime->avg(fn ($q) => $q->called_at->diffInMinutes($q->completed_at)));
        }

        $byService = $queues->groupBy('service_id')->map(fn ($items) => [
            'name' => $items->first()->service?->name ?? 'N/A',
            'total' => $items->count(),
        ])->values()->all();

        return [
            'total' => $total,
            'completed' => $completed,
            'skipped' => $skipped,
            'waiting' => $waiting,
            'called' => $called,
            'avg_mins' => $avgMins,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'by_service' => $byService,
        ];
    }
}
