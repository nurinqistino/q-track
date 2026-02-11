<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Walk-in queue only. Queue number generated daily per service.
     * Status: waiting, called, completed, skipped
     */
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('sequence')->comment('Daily sequence number per service (resets each day)');
            $table->string('status', 20)->default('waiting')->comment('waiting, called, completed, skipped');
            $table->foreignId('counter_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('ticket_date')->comment('Date for daily queue reset');
            $table->timestamps();

            $table->unique(['service_id', 'ticket_date', 'sequence']);
            $table->index(['service_id', 'status']);
            $table->index('ticket_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
