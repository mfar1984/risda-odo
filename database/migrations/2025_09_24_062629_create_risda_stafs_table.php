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
        Schema::create('risda_stafs', function (Blueprint $table) {
            $table->id();
            $table->string('no_pekerja')->unique();
            $table->string('nama_penuh');
            $table->string('no_kad_pengenalan', 14)->unique();
            $table->enum('jantina', ['lelaki', 'perempuan']);
            $table->unsignedBigInteger('bahagian_id');
            $table->unsignedBigInteger('stesen_id')->nullable();
            $table->string('jawatan');
            $table->string('no_telefon');
            $table->string('email')->unique();
            $table->string('no_fax')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif', 'gantung'])->default('aktif');

            // Alamat
            $table->string('alamat_1');
            $table->string('alamat_2')->nullable();
            $table->string('poskod', 5);
            $table->string('bandar');
            $table->string('negeri');
            $table->string('negara')->default('Malaysia');

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('bahagian_id')->references('id')->on('risda_bahagians')->onDelete('cascade');
            $table->foreign('stesen_id')->references('id')->on('risda_stesens')->onDelete('cascade');

            // Indexes for performance
            $table->index(['bahagian_id', 'stesen_id']);
            $table->index('status');
            $table->index('no_pekerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risda_stafs');
    }
};
