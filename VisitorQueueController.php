<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorQueueController extends Controller
{
    public function __construct(
        protected QueueService $queueService
    ) {}

    /**
     * Take a queue number for a service.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
        ]);

        $service = Service::where('id', $request->service_id)
            ->where('is_active', true)
            ->firstOrFail();

        $queue = $this->queueService->takeQueue($service);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Queue number generated.',
                'queue' => [
                    'id' => $queue->id,
                    'display_number' => $queue->display_number,
                    'service' => $queue->service->name,
                    'status' => $queue->status,
                    'ticket_date' => $queue->ticket_date->toDateString(),
                ],
            ], 201);
        }

        return redirect()->route('queue.display', $queue)->with('success', 'Queue number generated.');
    }

    /**
     * Display queue number.
     */
    public function show(Queue $queue): View
    {
        return view('visitor.queue-display', compact('queue'));
    }
}
