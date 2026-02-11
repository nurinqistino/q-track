<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $today = Carbon::today();
        $stats = $this->getKpiStats($today);
        $chartLine = $this->getQueueTrendsData();
        $chartBar = $this->getServiceDistributionData($today);
        $chartDoughnut = $this->getStatusBreakdownData($today);
        $activeQueues = $this->getActiveQueues($today);
        $staffStatus = User::whereIn('role', [User::ROLE_STAFF, User::ROLE_ADMIN])
            ->with('counters')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        $countersForAssign = Counter::where('is_active', true)
            ->orderBy('service_id')
            ->orderBy('number')
            ->get(['id', 'number', 'service_id'])
            ->groupBy('service_id')
            ->map(fn ($items) => $items->map(fn ($c) => ['id' => $c->id, 'number' => $c->number])->values()->all())
            ->toArray();

        return view('admin.dashboard', compact('stats', 'chartLine', 'chartBar', 'chartDoughnut', 'activeQueues', 'staffStatus', 'countersForAssign'));
    }

    /**
     * Assign a counter to a waiting queue (visitor) from admin dashboard.
     */
    public function assignCounter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'queue_id' => ['required', 'exists:queue_tickets,id'],
            'counter_id' => ['required', 'exists:counters,id'],
        ]);

        $queue = Queue::whereDate('ticket_date', Carbon::today())
            ->where('status', Queue::STATUS_WAITING)
            ->findOrFail($validated['queue_id']);

        $counter = Counter::where('is_active', true)->findOrFail($validated['counter_id']);

        if ((int) $counter->service_id !== (int) $queue->service_id) {
            return back()->with('error', 'Counter must be for the same service as the queue.');
        }

        // Satu counter = satu visitor "called" pada satu masa (elak duplicate assignment)
        $alreadyServing = Queue::whereDate('ticket_date', Carbon::today())
            ->where('counter_id', $counter->id)
            ->where('status', Queue::STATUS_CALLED)
            ->where('id', '!=', $queue->id)
            ->exists();
        if ($alreadyServing) {
            return back()->with('error', "Counter {$counter->number} is already serving another visitor. Complete or skip that queue first.");
        }

        $queue->update([
            'counter_id' => $counter->id,
            'status' => Queue::STATUS_CALLED,
            'called_at' => now(),
        ]);

        return back()->with('success', "Queue {$queue->display_number} assigned to Counter {$counter->number}.");
    }

    /**
     * JSON for dashboard refresh (real-time table, etc.)
     */
    public function dashboardData(): JsonResponse
    {
        $today = Carbon::today();
        return response()->json([
            'stats' => $this->getKpiStats($today),
            'active_queues' => $this->getActiveQueues($today),
            'chart_doughnut' => $this->getStatusBreakdownData($today),
        ]);
    }

    private function getKpiStats(Carbon $today): array
    {
        $todayQueues = Queue::whereDate('ticket_date', $today);
        $totalToday = (clone $todayQueues)->count();
        $completedToday = (clone $todayQueues)->where('status', Queue::STATUS_COMPLETED)->count();
        $yesterday = $today->copy()->subDay();
        $yesterdayCount = Queue::whereDate('ticket_date', $yesterday)->count();
        $lastWeekSameDay = $today->copy()->subWeek();
        $lastWeekCount = Queue::whereDate('ticket_date', $lastWeekSameDay)->count();

        $avgWaitMinutes = Queue::whereDate('ticket_date', $today)
            ->where('status', Queue::STATUS_COMPLETED)
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get()
            ->map(fn ($q) => $q->called_at && $q->completed_at ? $q->called_at->diffInMinutes($q->completed_at) : null)
            ->filter()
            ->values();
        $avgMins = $avgWaitMinutes->isEmpty() ? 0 : (int) round($avgWaitMinutes->avg());
        $completionRate = $totalToday > 0 ? round(($completedToday / $totalToday) * 100, 1) : 0;

        $todayDiff = $yesterdayCount > 0 ? round((($totalToday - $yesterdayCount) / $yesterdayCount) * 100) : 0;
        $weekDiff = $lastWeekCount > 0 ? round((($totalToday - $lastWeekCount) / $lastWeekCount) * 100) : 0;

        return [
            'today_queue_count' => $totalToday,
            'today_queue_trend' => $todayDiff,
            'total_visitors' => $totalToday,
            'visitors_week_trend' => $weekDiff,
            'avg_wait_mins' => $avgMins,
            'completed_today' => $completedToday,
            'completion_rate' => $completionRate,
            'services_count' => Service::count(),
            'counters_count' => Counter::count(),
            'staff_count' => User::where('role', User::ROLE_STAFF)->count(),
        ];
    }

    private function getQueueTrendsData(): array
    {
        $days = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('D');
            $data[] = Queue::whereDate('ticket_date', $date)->count();
        }
        return ['labels' => $days, 'data' => $data];
    }

    private function getServiceDistributionData(Carbon $today): array
    {
        $rows = Queue::whereDate('ticket_date', $today)
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->with('service:id,name,code')
            ->get();
        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $row->service ? $row->service->name : 'Unknown';
            $data[] = (int) $row->total;
        }
        if (empty($labels)) {
            $labels = ['No queue today'];
            $data = [0];
        }
        return ['labels' => $labels, 'data' => $data];
    }

    private function getStatusBreakdownData(Carbon $today): array
    {
        $rows = Queue::whereDate('ticket_date', $today)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        $labels = ['Waiting', 'Called', 'Completed', 'Skipped'];
        $statusKey = [
            Queue::STATUS_WAITING => 'Waiting',
            Queue::STATUS_CALLED => 'Called',
            Queue::STATUS_COMPLETED => 'Completed',
            Queue::STATUS_SKIPPED => 'Skipped',
        ];
        $data = [];
        foreach ($statusKey as $key => $label) {
            $data[] = (int) ($rows->get($key)->total ?? 0);
        }
        return ['labels' => $labels, 'data' => $data];
    }

    private function getActiveQueues(Carbon $today): array
    {
        return Queue::whereDate('ticket_date', $today)
            ->whereIn('status', [Queue::STATUS_WAITING, Queue::STATUS_CALLED])
            ->with(['service:id,name,code', 'counter' => fn ($c) => $c->with('users:id,name,staff_number')])
            ->orderByRaw("CASE status WHEN 'called' THEN 0 ELSE 1 END")
            ->orderBy('sequence')
            ->get()
            ->map(function ($q) {
                $staff = $q->counter && $q->counter->users->isNotEmpty()
                    ? $q->counter->users->first()
                    : null;
                return [
                    'id' => $q->id,
                    'display_number' => $q->display_number,
                    'service_id' => $q->service_id,
                    'service' => $q->service->name ?? '-',
                    'status' => $q->status,
                    'counter' => $q->counter ? (string) $q->counter->number : '-',
                    'staff_name' => $staff ? $staff->name : null,
                    'staff_id' => $staff ? ($staff->staff_number ?? '-') : null,
                    'wait_mins' => $q->called_at ? $q->called_at->diffInMinutes(now()) : null,
                ];
            })
            ->values()
            ->all();
    }
}
