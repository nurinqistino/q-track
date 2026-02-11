<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KWSP fixed services: EPF Withdrawals, Nomination, Contribution & Statement Enquiries, Employer Contribution Issues & Complaints
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Service code for queue display e.g. EPF, NOM, CON, EMP');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('common_issues')->nullable()->comment('Common issues visitors may encounter');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
