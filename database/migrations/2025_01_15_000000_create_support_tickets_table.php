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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number')->unique(); // Auto-generated ticket number
            $table->string('title'); // Subject/Title of the ticket
            $table->text('description'); // Description of the issue
            $table->json('categories'); // Array of categories (Account, Property, etc.)
            $table->json('attachments')->nullable(); // Array of image URLs
            $table->enum('status', ['open', 'in_progress', 'waiting_for_user', 'waiting_for_admin', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['created_at']);
            $table->index(['ticket_number']);
        });

        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who replied (user or admin)
            $table->text('message');
            $table->json('attachments')->nullable(); // Array of image URLs
            $table->boolean('is_admin_reply')->default(false); // To distinguish admin vs user replies
            $table->timestamps();
            
            // Indexes
            $table->index(['support_ticket_id', 'created_at']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
}; 