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
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('page_name'); // e.g., "Home Page", "Property / Search"
            $table->text('page_url'); // Full URL with query parameters
            $table->text('user_agent'); // Browser/device information
            $table->timestamp('visited_at'); // When the page was visited
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id']);
            $table->index(['page_name']);
            $table->index(['visited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_sources');
    }
}; 