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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->string('category'); // e.g., Technical, Account, Admin, System, Data, Other
            $table->string('priority')->default('rendah'); // e.g., rendah, sederhana, tinggi, kritikal
            $table->string('status')->default('baru'); // e.g., baru, dalam_proses, dijawab, ditutup, escalated

            // Multi-tenancy context of the creator
            $table->string('jenis_organisasi')->nullable(); // 'bahagian', 'stesen', 'semua'
            $table->unsignedBigInteger('organisasi_id')->nullable(); // ID of bahagian/stesen

            $table->unsignedBigInteger('created_by'); // User who created the ticket
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('assigned_to')->nullable(); // Admin user assigned to the ticket
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');

            $table->json('attachments')->nullable(); // JSON array of file paths
            $table->string('source')->default('web'); // 'web', 'android'

            $table->timestamp('last_reply_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};


