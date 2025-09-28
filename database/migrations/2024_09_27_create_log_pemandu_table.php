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
        Schema::create('log_pemandu', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('pemandu_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kenderaan_id')->constrained('kenderaan')->onDelete('cascade');
            
            // Maklumat Perjalanan
            $table->date('tarikh_perjalanan');
            $table->time('masa_keluar');
            $table->time('masa_masuk')->nullable();
            $table->string('destinasi');
            $table->text('catatan')->nullable();
            
            // Maklumat Odometer
            $table->integer('odometer_keluar');
            $table->integer('odometer_masuk')->nullable();
            $table->integer('jarak')->nullable(); // Auto-calculated
            
            // Maklumat Minyak
            $table->decimal('liter_minyak', 8, 2)->nullable();
            $table->decimal('kos_minyak', 10, 2)->nullable();
            $table->string('stesen_minyak')->nullable();
            $table->string('resit_minyak')->nullable(); // File path
            
            // Status & Organization
            $table->enum('status', ['dalam_perjalanan', 'selesai', 'tertunda'])->default('dalam_perjalanan');
            $table->string('organisasi_id')->nullable();
            
            // Audit Fields
            $table->foreignId('dicipta_oleh')->constrained('users')->onDelete('cascade');
            $table->foreignId('dikemaskini_oleh')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tarikh_perjalanan', 'status']);
            $table->index(['pemandu_id', 'tarikh_perjalanan']);
            $table->index(['kenderaan_id', 'tarikh_perjalanan']);
            $table->index('organisasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_pemandu');
    }
};
