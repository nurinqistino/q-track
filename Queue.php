<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_CALLED = 'called';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SKIPPED = 'skipped';

    protected $table = 'queue_tickets';

    protected $fillable = [
        'service_id',
        'counter_id',
        'sequence',
        'status',
        'ticket_date',
        'called_at',
        'completed_at',
    ];

    protected $casts = [
        'ticket_date' => 'date',
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the service that owns the queue ticket.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the counter serving this queue ticket (nullable when waiting).
     */
    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    /**
     * Get the display queue number (e.g. EPF001, NOM002).
     */
    public function getDisplayNumberAttribute(): string
    {
        $code = $this->service?->code ?? 'N/A';

        return $code . str_pad((string) $this->sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if status can transition to completed or skipped.
     */
    public function canCompleteOrSkip(): bool
    {
        return $this->status === self::STATUS_CALLED;
    }
}
