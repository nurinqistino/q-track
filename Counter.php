<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'service_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the service that owns the counter.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff assigned to this counter.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'counter_user')
            ->withTimestamps();
    }

    /**
     * Get the queue tickets served by this counter.
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }
}
