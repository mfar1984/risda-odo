<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_usage_reports', function (Blueprint $table) {
            $table->unsignedInteger('num_pages')->default(1)->after('summary');
            $table->string('no_siri_from')->nullable()->after('no_siri');
            $table->string('no_siri_to')->nullable()->after('no_siri_from');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_usage_reports', function (Blueprint $table) {
            $table->dropColumn(['num_pages', 'no_siri_from', 'no_siri_to']);
        });
    }
};


