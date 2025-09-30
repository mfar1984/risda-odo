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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('kumpulan_id')->nullable()->after('password');
            $table->enum('jenis_organisasi', ['hq', 'negeri', 'bahagian', 'stesen'])->nullable()->after('kumpulan_id');
            $table->unsignedBigInteger('organisasi_id')->nullable()->after('jenis_organisasi');
            $table->enum('status', ['aktif', 'tidak_aktif', 'gantung'])->default('aktif')->after('organisasi_id');
            
            // Foreign key constraints
            $table->foreign('kumpulan_id')->references('id')->on('user_groups')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['jenis_organisasi', 'organisasi_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kumpulan_id']);
            $table->dropIndex(['jenis_organisasi', 'organisasi_id']);
            $table->dropIndex(['status']);
            $table->dropColumn(['kumpulan_id', 'jenis_organisasi', 'organisasi_id', 'status']);
        });
    }
};
