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
        Schema::create('cuti_umum_override', function (Blueprint $table) {
            $table->id();
            $table->date('tarikh_mula');
            $table->date('tarikh_akhir');
            $table->string('nama_cuti');
            $table->string('negeri'); // 'Semua', 'Sarawak', 'Sabah', etc
            $table->text('catatan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->unsignedBigInteger('dicipta_oleh')->nullable();
            $table->timestamps();

            $table->foreign('dicipta_oleh')->references('id')->on('users')->onDelete('set null');
            $table->index(['tarikh_mula', 'tarikh_akhir', 'negeri']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti_umum_override');
    }
};
