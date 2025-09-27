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
        Schema::create('kenderaans', function (Blueprint $table) {
            $table->id();
            $table->string('no_plat')->unique();
            $table->string('jenama');
            $table->string('model');
            $table->year('tahun');
            $table->string('no_enjin');
            $table->string('no_casis');
            $table->enum('jenis_bahan_api', ['petrol', 'diesel']);
            $table->string('kapasiti_muatan')->nullable();
            $table->string('warna');
            $table->date('cukai_tamat_tempoh');
            $table->date('tarikh_pendaftaran');
            $table->enum('status', ['aktif', 'tidak_aktif', 'penyelenggaraan'])->default('aktif');
            $table->json('dokumen_kenderaan')->nullable(); // Store file paths
            $table->foreignId('dicipta_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenderaans');
    }
};
