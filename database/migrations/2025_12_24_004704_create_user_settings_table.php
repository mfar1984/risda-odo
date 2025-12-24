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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Data & Eksport Settings
            $table->enum('format_eksport', ['pdf', 'excel', 'csv'])->default('pdf');
            $table->enum('format_tarikh', ['DD/MM/YYYY', 'DD-MM-YYYY', 'YYYY-MM-DD', 'DD MMM YYYY'])->default('DD/MM/YYYY');
            $table->enum('format_masa', ['24', '12'])->default('24');
            $table->enum('format_nombor', ['1,234.56', '1.234,56', '1 234.56'])->default('1,234.56');
            $table->string('mata_wang', 10)->default('MYR');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
