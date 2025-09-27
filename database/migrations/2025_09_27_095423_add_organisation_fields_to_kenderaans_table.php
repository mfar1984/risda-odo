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
        Schema::table('kenderaans', function (Blueprint $table) {
            // Add organisational fields
            $table->foreignId('bahagian_id')->nullable()->constrained('risda_bahagians')->after('dicipta_oleh');
            $table->foreignId('stesen_id')->nullable()->constrained('risda_stesens')->after('bahagian_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kenderaans', function (Blueprint $table) {
            $table->dropForeign(['stesen_id']);
            $table->dropForeign(['bahagian_id']);
            $table->dropColumn(['stesen_id', 'bahagian_id']);
        });
    }
};
