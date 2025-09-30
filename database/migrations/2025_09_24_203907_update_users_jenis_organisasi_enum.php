<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update jenis_organisasi ENUM to include 'semua'
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN jenis_organisasi ENUM('hq','negeri','bahagian','stesen','semua') NULL");
            return;
        }

        // SQLite (used for automated tests) does not support MODIFY COLUMN.
        // The original column is stored as TEXT, so no structural change is required.
        // We keep the migration idempotent by skipping modification for other drivers.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Revert back to original ENUM values
            DB::statement("ALTER TABLE users MODIFY COLUMN jenis_organisasi ENUM('hq','negeri','bahagian','stesen') NULL");
        }
    }
};
