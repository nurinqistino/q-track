<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE users RENAME COLUMN id_number TO staff_number');
        } else {
            DB::statement('ALTER TABLE users CHANGE id_number staff_number VARCHAR(20) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE users RENAME COLUMN staff_number TO id_number');
        } else {
            DB::statement('ALTER TABLE users CHANGE staff_number id_number VARCHAR(20) NULL');
        }
    }
};
