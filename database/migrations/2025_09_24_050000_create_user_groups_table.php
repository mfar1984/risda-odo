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
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kumpulan');
            $table->json('kebenaran_matrix'); // Permission matrix
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif', 'gantung'])->default('aktif');
            $table->unsignedBigInteger('dicipta_oleh');
            $table->enum('jenis_organisasi', ['semua', 'bahagian', 'stesen'])->nullable();
            $table->unsignedBigInteger('organisasi_id')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('dicipta_oleh')->references('id')->on('users');
            $table->foreign('organisasi_id')->references('id')->on('risda_bahagians')->nullOnDelete();

            // Indexes for performance
            $table->index('status');
            $table->index(['jenis_organisasi', 'organisasi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};
