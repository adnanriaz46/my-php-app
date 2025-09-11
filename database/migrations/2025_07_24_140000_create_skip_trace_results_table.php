<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skip_trace_results', function (Blueprint $table) {
            $table->id();
            $table->string('investor_identifier'); // LLC name or investor identifier
            $table->string('mailing_address'); // Mailing address used for skip trace
            $table->string('mailing_city');
            $table->string('mailing_state');
            $table->string('mailing_zip')->nullable();
            
            // Skip trace results
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('age')->nullable();
            $table->boolean('deceased')->default(false);
            $table->json('phone_numbers')->nullable();
            $table->json('phone_type')->nullable();
            $table->json('emails')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->json('connected_people')->nullable();
            
            // Metadata
            $table->timestamp('last_used_at')->nullable(); // Track when this data was last accessed
            $table->integer('usage_count')->default(1); // Track how many times this data has been used
            $table->timestamps();
            
            // Create unique constraint on investor_identifier (one skip trace record per LLC)
            $table->unique('investor_identifier', 'unique_skip_trace_investor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skip_trace_results');
    }
}; 