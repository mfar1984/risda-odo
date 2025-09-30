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
        Schema::create('selenggara_kenderaan', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('kenderaan_id')->constrained('kenderaans')->onDelete('cascade');
            $table->foreignId('kategori_kos_id')->constrained('kategori_kos_selenggara')->onDelete('restrict');
            $table->foreignId('dilaksana_oleh')->nullable()->constrained('users')->onDelete('set null'); // User who performed/recorded maintenance
            
            // Organizational Hierarchy (for multi-tenancy)
            $table->enum('jenis_organisasi', ['semua', 'bahagian', 'stesen']);
            $table->unsignedBigInteger('organisasi_id')->nullable(); // bahagian_id or stesen_id
            
            // Maintenance Details
            $table->date('tarikh_mula');
            $table->date('tarikh_selesai');
            $table->decimal('jumlah_kos', 10, 2);
            $table->text('keterangan')->nullable();
            
            // Oil Change Specific
            $table->boolean('tukar_minyak')->default(false);
            $table->integer('jangka_hayat_km')->nullable(); // Lifespan in KM (e.g., 5000 km)
            
            // Invoice/Receipt
            $table->string('fail_invois')->nullable(); // File path for uploaded invoice
            
            // Status
            $table->enum('status', ['dijadualkan', 'dalam_proses', 'selesai'])->default('selesai');
            
            $table->timestamps();
            
            // Indexes for performance with custom names
            $table->index(['kenderaan_id', 'tarikh_mula', 'tarikh_selesai'], 'idx_selenggara_kenderaan_tarikh');
            $table->index(['jenis_organisasi', 'organisasi_id'], 'idx_selenggara_organisasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selenggara_kenderaan');
    }
};
