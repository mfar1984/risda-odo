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
        Schema::create('tuntutan', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to log_pemandu
            $table->foreignId('log_pemandu_id')
                ->constrained('log_pemandu')
                ->onDelete('cascade')
                ->comment('FK to driver log entry');
            
            // Claim details
            $table->enum('kategori', [
                'tol',
                'parking',
                'f&b',
                'accommodation',
                'fuel',
                'car_maintenance',
                'others'
            ])->comment('Claim category');
            
            $table->decimal('jumlah', 10, 2)->comment('Claim amount');
            $table->text('keterangan')->nullable()->comment('Description/notes');
            $table->string('resit')->nullable()->comment('Receipt image path');
            
            // Status and workflow
            $table->enum('status', [
                'pending',
                'diluluskan',
                'ditolak',
                'digantung'
            ])->default('pending')->comment('Claim status');
            
            $table->text('alasan_tolak')->nullable()->comment('Rejection reason (if status=ditolak)');
            $table->text('alasan_gantung')->nullable()->comment('Cancellation reason (if status=digantung)');
            
            // Audit trail
            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who approved/rejected/cancelled');
            
            $table->timestamp('tarikh_diproses')->nullable()->comment('Date of approval/rejection/cancellation');
            
            // Soft delete
            $table->softDeletes();
            
            $table->timestamps();
            
            // Indexes
            $table->index('log_pemandu_id');
            $table->index('kategori');
            $table->index('status');
            $table->index('diproses_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuntutan');
    }
};
