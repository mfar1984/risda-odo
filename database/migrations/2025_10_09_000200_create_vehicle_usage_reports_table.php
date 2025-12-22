<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_usage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('no_siri'); // e.g. A 316321
            $table->unsignedBigInteger('kenderaan_id');
            $table->date('bulan'); // store first day of month for indexing
            $table->json('header'); // jenama+model, no_plat, bahagian text
            $table->json('rows'); // table rows snapshot
            $table->json('summary'); // totals & kadar
            $table->unsignedBigInteger('disimpan_oleh');
            $table->timestamps();

            $table->index(['kenderaan_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_usage_reports');
    }
};


