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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('notification_type_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('severity', ['info', 'success', 'warning', 'error'])->default('info');
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Action related fields
            $table->string('action_label')->nullable();
            $table->string('action_url')->nullable();
            $table->json('action_data')->nullable(); // For storing additional action data
            
            // Related entity fields
            $table->string('related_entity_type')->nullable(); // property, user, campaign, etc.
            $table->unsignedBigInteger('related_entity_id')->nullable(); // ID of the related entity
            
            // Additional metadata
            $table->json('metadata')->nullable(); // For storing additional data
            $table->timestamp('scheduled_at')->nullable(); // For scheduled notifications
            $table->timestamp('sent_at')->nullable(); // When notification was actually sent
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'read']);
            $table->index(['user_id', 'created_at']);
            $table->index(['notification_type_id']);
            $table->index(['related_entity_type', 'related_entity_id']);
            $table->index(['scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
