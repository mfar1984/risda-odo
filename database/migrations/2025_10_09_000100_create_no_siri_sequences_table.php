<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('no_siri_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('prefix', 8)->default('A');
            $table->unsignedBigInteger('current_number')->default(316320); // next will be 316321
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_siri_sequences');
    }
};


