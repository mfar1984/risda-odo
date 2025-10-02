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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('staf_id')->nullable()->after('email')->constrained('risda_stafs')->onDelete('cascade');
            $table->index('staf_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['staf_id']);
            $table->dropIndex(['staf_id']);
            $table->dropColumn('staf_id');
        });
    }
};
