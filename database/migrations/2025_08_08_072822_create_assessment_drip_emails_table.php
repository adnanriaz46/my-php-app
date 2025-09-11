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
        Schema::create('assessment_drip_emails', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to assessment_responses
            $table->unsignedBigInteger('assessment_response_id');
            
            // Drip email step (1-20 based on the JSON data)
            $table->integer('step');
            
            // Email content
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            
            // Email status and tracking
            $table->enum('status', ['pending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
            
            // Indexes
            $table->index('assessment_response_id');
            $table->index('step');
            $table->index('status');
            $table->index(['assessment_response_id', 'step']);
            
            // Foreign key constraint
            $table->foreign('assessment_response_id')
                  ->references('id')
                  ->on('assessment_responses')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_drip_emails');
    }
};
