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
        DB::statement("ALTER TABLE users MODIFY COLUMN jenis_organisasi ENUM('hq','negeri','bahagian','stesen','semua') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE users MODIFY COLUMN jenis_organisasi ENUM('hq','negeri','bahagian','stesen') NULL");
    }
};
