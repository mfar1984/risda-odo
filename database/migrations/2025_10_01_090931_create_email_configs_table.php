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
        Schema::create('email_configs', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->enum('jenis_organisasi', ['semua', 'bahagian', 'stesen'])->default('semua');
            $table->string('organisasi_id')->nullable();
            
            // SMTP Configuration
            $table->string('smtp_host');
            $table->integer('smtp_port')->default(587);
            $table->string('smtp_encryption', 10)->nullable(); // tls, ssl, null
            $table->boolean('smtp_authentication')->default(true);
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // Encrypted
            $table->string('smtp_from_address');
            $table->string('smtp_from_name');
            $table->string('smtp_reply_to')->nullable();
            $table->integer('smtp_connection_timeout')->default(30); // seconds
            $table->integer('smtp_max_retries')->default(3);
            
            // Testing
            $table->timestamp('smtp_last_test')->nullable();
            $table->enum('smtp_test_status', ['success', 'failed', 'pending'])->nullable();
            $table->text('smtp_test_message')->nullable();
            
            // Status
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            
            // Audit
            $table->foreignId('dicipta_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dikemaskini_oleh')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['jenis_organisasi', 'organisasi_id']);
            $table->index('status');
        });

        // Insert default config for Administrator (semua)
        DB::table('email_configs')->insert([
            'jenis_organisasi' => 'semua',
            'organisasi_id' => null,
            'smtp_host' => 'smtp.office365.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'smtp_authentication' => true,
            'smtp_from_address' => 'noreply@risda.gov.my',
            'smtp_from_name' => 'RISDA Odometer System',
            'smtp_connection_timeout' => 30,
            'smtp_max_retries' => 3,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_configs');
    }
};
