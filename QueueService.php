<?php

namespace App\Services;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Take a queue number for a service (visitor action).
     */
    public function takeQueue(Service $service): Queue
    {
        $today = Carbon::today()->toDateString();

        $sequence = DB::transaction(function () use ($service, $today) {
            $maxSequence = Queue::where('service_id', $service->id)
                ->where('ticket_date', $today)
                ->max('sequence');

            return ($maxSequence ?? 0) + 1;
        });

        return Queue::create([
            'service_id' => $service->id,
            'sequence' => $sequence,
            'status' => Queue::STATUS_WAITING,
            'ticket_date' => $today,
        ]);
    }

    /**
     * Call next waiting queue for the counter (staff action).
     * Staff can only call queue for their assigned counter's service.
     */
    public function callNext(Counter $counter, User $staff): ?Queue
    {
        $this->ensureStaffAssignedToCounter($staff, $counter);

        $nextQueue = Queue::where('service_id', $counter->service_id)
            ->where('status', Queue::STATUS_WAITING)
            ->whereDate('ticket_date', Carbon::today())
            ->orderBy('created_at')
            ->first();

        if (! $nextQueue) {
            return null;
        }

        $nextQueue->update([
            'status' => Queue::STATUS_CALLED,
            'counter_id' => $counter->id,
            'called_at' => now(),
        ]);

        return $nextQueue->fresh(['service', 'counter']);
    }

    /**
     * Mark queue as completed (staff action).
     */
    public function complete(Queue $queue, User $staff): Queue
    {
        $this->ensureStaffCanManageQueue($queue, $staff);
        $this->ensureQueueCanCompleteOrSkip($queue);

        $queue->update([
            'status' => Queue::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return $queue->fresh(['service', 'counter']);
    }

    /**
     * Mark queue as skipped (staff action).
     */
    public function skip(Queue $queue, User $staff): Queue
    {
        $this->ensureStaffCanManageQueue($queue, $staff);
        $this->ensureQueueCanCompleteOrSkip($queue);

        $queue->update([
            'status' => Queue::STATUS_SKIPPED,
            'completed_at' => now(),
        ]);

        return $queue->fresh(['service', 'counter']);
    }

    /**
     * Ensure staff is assigned to the counter.
     */
    protected function ensureStaffAssignedToCounter(User $staff, Counter $counter): void
    {
        if (! $staff->counters()->where('counters.id', $counter->id)->exists()) {
            abort(403, 'You are not assigned to this counter.');
        }
    }

    /**
     * Ensure staff can manage this queue (assigned to the queue's counter).
     */
    protected function ensureStaffCanManageQueue(Queue $queue, User $staff): void
    {
        if (! $queue->counter_id) {
            abort(403, 'Queue has no assigned counter.');
        }

        if (! $staff->counters()->where('counters.id', $queue->counter_id)->exists()) {
            abort(403, 'You can only manage queues for your assigned counter.');
        }
    }

    /**
     * Ensure queue is in 'called' status before complete/skip.
     */
    protected function ensureQueueCanCompleteOrSkip(Queue $queue): void
    {
        if (! $queue->canCompleteOrSkip()) {
            abort(422, 'Only called queues can be completed or skipped.');
        }
    }
}
