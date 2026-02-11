<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        $counters = Counter::where('is_active', true)->with('service')->orderBy('service_id')->orderBy('number')->get();

        return view('board.index', compact('counters'));
    }

    public function data(): JsonResponse
    {
        $today = Carbon::today();

        $called = Queue::where('status', 'called')
            ->whereDate('ticket_date', $today)
            ->whereNotNull('counter_id')
            ->with('service', 'counter')
            ->get()
            ->map(fn ($q) => [
                'counter_id' => $q->counter_id,
                'display_number' => $q->display_number,
                'service' => $q->service->name,
                'counter_number' => $q->counter->number,
            ]);

        $waiting = Queue::where('status', 'waiting')->whereDate('ticket_date', $today)->count();
        $completed = Queue::where('status', 'completed')->whereDate('ticket_date', $today)->count();

        return response()->json([
            'called' => $called,
            'waiting' => $waiting,
            'completed_today' => $completed,
        ]);
    }
}
