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
        Schema::create('integrasi_config', function (Blueprint $table) {
            $table->id();
            
            // API Configuration
            $table->string('api_token', 100)->nullable()->unique();
            $table->timestamp('api_token_created_at')->nullable();
            $table->timestamp('api_token_last_used')->nullable();
            $table->integer('api_token_usage_count')->default(0);
            
            // SMTP Configuration
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable()->default(587);
            $table->string('smtp_encryption', 10)->nullable()->default('tls');
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // Encrypted
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            
            // Audit
            $table->foreignId('dikemaskini_oleh')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });

        // Insert default record
        DB::table('integrasi_config')->insert([
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrasi_config');
    }
};
