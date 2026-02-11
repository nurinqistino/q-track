<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Maps staff (KWSP officers) to their assigned counters.
     * Staff can only manage queues for their assigned counter(s).
     */
    public function up(): void
    {
        Schema::create('counter_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counter_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['counter_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_user');
    }
};
