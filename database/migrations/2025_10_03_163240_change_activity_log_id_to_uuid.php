<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing data first (important!)
        \DB::table('activity_log')->truncate();
        
        // Use raw SQL to modify the column
        \DB::statement('ALTER TABLE activity_log MODIFY id BIGINT UNSIGNED NOT NULL');
        \DB::statement('ALTER TABLE activity_log DROP PRIMARY KEY');
        \DB::statement('ALTER TABLE activity_log DROP COLUMN id');
        \DB::statement('ALTER TABLE activity_log ADD id CHAR(36) PRIMARY KEY FIRST');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop existing data
        \DB::table('activity_log')->truncate();
        
        // Use raw SQL to restore
        \DB::statement('ALTER TABLE activity_log DROP PRIMARY KEY');
        \DB::statement('ALTER TABLE activity_log DROP COLUMN id');
        \DB::statement('ALTER TABLE activity_log ADD id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');
    }
};
