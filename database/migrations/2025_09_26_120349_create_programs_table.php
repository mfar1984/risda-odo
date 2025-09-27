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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            // Maklumat Program
            $table->string('nama_program');
            $table->enum('status', ['aktif', 'tertunda', 'selesai'])->default('aktif');
            $table->date('tarikh_mula');
            $table->date('tarikh_selesai');
            $table->string('lokasi_program');
            $table->text('penerangan')->nullable();

            // Permohonan & Tugasan
            $table->foreignId('permohonan_dari')->constrained('risda_stafs'); // Staff yang mohon
            $table->foreignId('pemandu_id')->constrained('risda_stafs'); // Staff yang jadi pemandu
            $table->foreignId('kenderaan_id')->constrained('kenderaans'); // Kenderaan yang digunakan

            // Data Isolation Fields
            $table->enum('jenis_organisasi', ['semua', 'bahagian', 'stesen'])->default('stesen');
            $table->unsignedBigInteger('organisasi_id')->nullable(); // bahagian_id atau stesen_id

            // Audit Fields
            $table->foreignId('dicipta_oleh')->constrained('users');
            $table->foreignId('dikemaskini_oleh')->nullable()->constrained('users');

            $table->timestamps();

            // Indexes for performance
            $table->index(['jenis_organisasi', 'organisasi_id']);
            $table->index('status');
            $table->index(['tarikh_mula', 'tarikh_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
