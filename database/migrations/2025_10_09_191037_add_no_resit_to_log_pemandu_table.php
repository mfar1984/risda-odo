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
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->string('no_resit')->nullable()->after('resit_minyak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->dropColumn('no_resit');
        });
    }
};
