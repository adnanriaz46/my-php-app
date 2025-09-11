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
        Schema::create('campaign_unsubscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Email address that was unsubscribed
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('cascade'); // Optional: specific campaign
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Optional: if user was registered
            $table->text('reason')->nullable(); // Optional reason for unsubscription
            $table->string('ip_address')->nullable(); // IP address of the unsubscription request
            $table->text('user_agent')->nullable(); // User agent of the request
            $table->boolean('is_global')->default(false); // Whether this is a global unsubscription
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('email');
            $table->index(['email', 'campaign_id']);
            $table->index('is_global');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_unsubscriptions');
    }
};
