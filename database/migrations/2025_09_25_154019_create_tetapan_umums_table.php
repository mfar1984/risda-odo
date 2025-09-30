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
        Schema::create('tetapan_umums', function (Blueprint $table) {
            $table->id();

            // Tetapan Umum fields
            $table->string('nama_sistem')->default('RISDA Odometer System');
            $table->string('versi_sistem')->default('1.0.0');
            $table->string('alamat_1')->nullable();
            $table->string('alamat_2')->nullable();
            $table->string('poskod', 10)->nullable();
            $table->string('bandar')->nullable();
            $table->string('negeri')->nullable();
            $table->string('negara')->default('Malaysia');

            // Tetapan Sistem fields
            $table->integer('maksimum_percubaan_login')->default(3);
            $table->integer('masa_tamat_sesi_minit')->default(60);

            // Organization isolation fields
            $table->enum('jenis_organisasi', ['semua', 'bahagian', 'stesen'])->default('semua');
            $table->unsignedBigInteger('organisasi_id')->nullable();

            // Audit fields
            $table->unsignedBigInteger('dicipta_oleh');
            $table->unsignedBigInteger('dikemaskini_oleh')->nullable();

            $table->json('mata_hubungan')->nullable();
            $table->json('media_sosial')->nullable();
            $table->json('konfigurasi_notifikasi')->nullable();
            $table->string('map_provider')->nullable();
            $table->string('map_api_key')->nullable();
            $table->string('map_style_url')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('dicipta_oleh')->references('id')->on('users');
            $table->foreign('dikemaskini_oleh')->references('id')->on('users');

            // Index for organization filtering
            $table->index(['jenis_organisasi', 'organisasi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tetapan_umums');
    }
};
