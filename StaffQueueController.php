<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Queue;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffQueueController extends Controller
{
    public function __construct(
        protected QueueService $queueService
    ) {}

    /**
     * Staff dashboard (counter operator cockpit).
     */
    public function index(Request $request): View
    {
        $counters = $request->user()->counters()->where('is_active', true)->with('service')->orderBy('service_id')->orderBy('number')->get();
        $counterIds = $counters->pluck('id');

        $today = Carbon::today();
        $calledQueues = Queue::where('status', 'called')
            ->whereDate('ticket_date', $today)
            ->whereIn('counter_id', $counterIds)
            ->with('service', 'counter')
            ->get()
            ->keyBy('counter_id');

        $nextInQueueByCounter = [];
        foreach ($counters as $counter) {
            $nextInQueueByCounter[$counter->id] = Queue::where('status', 'waiting')
                ->whereDate('ticket_date', $today)
                ->where('service_id', $counter->service_id)
                ->with('service')
                ->orderBy('sequence')
                ->limit(5)
                ->get();
        }

        $servedToday = Queue::whereIn('counter_id', $counterIds)
            ->whereDate('ticket_date', $today)
            ->whereIn('status', [Queue::STATUS_COMPLETED, Queue::STATUS_SKIPPED])
            ->count();

        $completedToday = Queue::whereIn('counter_id', $counterIds)
            ->whereDate('ticket_date', $today)
            ->where('status', Queue::STATUS_COMPLETED)
            ->get();
        $avgServiceMins = $completedToday->filter(fn ($q) => $q->called_at && $q->completed_at)
            ->map(fn ($q) => $q->called_at->diffInMinutes($q->completed_at))
            ->values();
        $avgMins = $avgServiceMins->isEmpty() ? 0 : (int) round($avgServiceMins->avg());
        $noShowToday = Queue::whereIn('counter_id', $counterIds)
            ->whereDate('ticket_date', $today)
            ->where('status', Queue::STATUS_SKIPPED)
            ->count();

        return view('staff.dashboard', compact(
            'counters',
            'calledQueues',
            'nextInQueueByCounter',
            'servedToday',
            'avgMins',
            'noShowToday'
        ));
    }

    /**
     * Call next waiting queue for the counter.
     */
    public function callNext(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'counter_id' => ['required', 'exists:counters,id'],
        ]);

        $counter = Counter::with('service')->findOrFail($request->counter_id);

        if (! $counter->is_active) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Counter is not active.'], 422)
                : back()->with('error', 'Counter is not active.');
        }

        $queue = $this->queueService->callNext($counter, $request->user());

        if (! $queue) {
            return $request->expectsJson()
                ? response()->json(['message' => 'No waiting queue for this service.'], 200)
                : back()->with('info', 'No waiting queue for this service.');
        }

        return $request->expectsJson()
            ? response()->json([
                'message' => 'Queue called.',
                'queue' => [
                    'id' => $queue->id,
                    'display_number' => $queue->display_number,
                    'service' => $queue->service->name,
                    'counter_number' => $queue->counter->number,
                    'status' => $queue->status,
                    'called_at' => $queue->called_at?->toIso8601String(),
                ],
            ])
            : back()->with('success', "Queue {$queue->display_number} called.");
    }

    /**
     * Mark queue as completed.
     */
    public function complete(Request $request, Queue $queue): JsonResponse|RedirectResponse
    {
        $queue = $this->queueService->complete($queue, $request->user());

        return $request->expectsJson()
            ? response()->json([
                'message' => 'Queue completed.',
                'queue' => [
                    'id' => $queue->id,
                    'display_number' => $queue->display_number,
                    'status' => $queue->status,
                    'completed_at' => $queue->completed_at?->toIso8601String(),
                ],
            ])
            : back()->with('success', "Queue {$queue->display_number} completed.");
    }

    /**
     * Mark queue as skipped.
     */
    public function skip(Request $request, Queue $queue): JsonResponse|RedirectResponse
    {
        $queue = $this->queueService->skip($queue, $request->user());

        return $request->expectsJson()
            ? response()->json([
                'message' => 'Queue skipped.',
                'queue' => [
                    'id' => $queue->id,
                    'display_number' => $queue->display_number,
                    'status' => $queue->status,
                    'completed_at' => $queue->completed_at?->toIso8601String(),
                ],
            ])
            : back()->with('success', "Queue {$queue->display_number} skipped.");
    }

    /**
     * Toggle staff active / non-active status (for admin dashboard visibility).
     */
    public function toggleStatus(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'Active' : 'Non-active';
        return back()->with('success', "Status set to {$status}.");
    }
}
