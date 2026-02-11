<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class VisitorController extends Controller
{
    /**
     * Visitor dashboard (home): services, Get Queue, View Board, stats.
     */
    public function dashboard(): View
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $stats = $this->getStats();

        return view('welcome', compact('services', 'stats'));
    }

    /**
     * JSON stats for Current Wait, Active Counters, Avg Service Time.
     */
    public function dashboardStats(): JsonResponse
    {
        return response()->json($this->getStats());
    }

    private function getStats(): array
    {
        $today = Carbon::today();
        $currentWait = Queue::where('status', 'waiting')
            ->whereDate('ticket_date', $today)
            ->count();
        $activeCounters = Counter::where('is_active', true)->count();

        $services = Service::where('is_active', true)->get();
        $total = 0;
        $count = 0;
        foreach ($services as $s) {
            if (! $s->estimated_time) {
                continue;
            }
            if (preg_match('/^(\d+)\s*-\s*(\d+)/', $s->estimated_time, $m)) {
                $total += ((int) $m[1] + (int) $m[2]) / 2;
                $count++;
            } elseif (preg_match('/(\d+)/', $s->estimated_time, $m)) {
                $total += (int) $m[1];
                $count++;
            }
        }
        $avgMinutes = $count > 0 ? (int) round($total / $count) : 15;

        return [
            'current_wait' => $currentWait,
            'active_counters' => $activeCounters,
            'avg_service_time' => $avgMinutes,
        ];
    }

    /**
     * Select service page (for Get Queue Number flow).
     */
    public function services(): View
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('visitor.services', compact('services'));
    }
}
