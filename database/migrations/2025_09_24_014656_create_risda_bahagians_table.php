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
        Schema::create('risda_bahagians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahagian');
            $table->string('no_telefon');
            $table->string('email');
            $table->string('no_fax')->nullable();
            $table->string('status');
            $table->enum('status_dropdown', ['aktif', 'tidak_aktif', 'dalam_pembinaan']);
            $table->string('alamat_1');
            $table->string('alamat_2')->nullable();
            $table->string('poskod', 5);
            $table->string('bandar');
            $table->string('negeri');
            $table->string('negara')->default('Malaysia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risda_bahagians');
    }
};
