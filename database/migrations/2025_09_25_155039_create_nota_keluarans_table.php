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
        Schema::create('nota_keluarans', function (Blueprint $table) {
            $table->id();

            // Version information
            $table->string('versi', 20)->unique(); // e.g., "1.2.0", "1.1.0"
            $table->string('nama_versi')->nullable(); // e.g., "Nota Keluaran Blue"
            $table->enum('jenis_keluaran', ['blue', 'green'])->default('green'); // Blue = latest, Green = major/minor
            $table->date('tarikh_keluaran');
            $table->text('penerangan')->nullable(); // Version description

            // Release content
            $table->json('ciri_baharu')->nullable(); // New features array
            $table->json('penambahbaikan')->nullable(); // Improvements array
            $table->json('pembetulan_pepijat')->nullable(); // Bug fixes array
            $table->json('perubahan_teknikal')->nullable(); // Technical changes array

            // Status and metadata
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_latest')->default(false); // Mark latest version
            $table->integer('urutan')->default(0); // Sort order

            // Audit fields
            $table->unsignedBigInteger('dicipta_oleh');
            $table->unsignedBigInteger('dikemaskini_oleh')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('dicipta_oleh')->references('id')->on('users');
            $table->foreign('dikemaskini_oleh')->references('id')->on('users');

            // Indexes
            $table->index(['status', 'is_latest']);
            $table->index('tarikh_keluaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_keluarans');
    }
};
