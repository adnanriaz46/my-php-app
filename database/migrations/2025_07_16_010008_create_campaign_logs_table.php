<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->jsonb('recipients_ids')->nullable(); // Array of recipient IDs
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('from'); // Email address
            $table->text('errors')->nullable(); // Error messages
            $table->string('sendgrid_email_id')->nullable(); // SendGrid message ID
            $table->boolean('success')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_logs');
    }
};
