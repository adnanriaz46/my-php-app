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
        Schema::create('county_states', function (Blueprint $table) {
            $table->id();
            $table->text('county');
            $table->text('display');
            $table->text('fips')->nullable();
            $table->text('state');
            $table->text('slug');
            $table->timestamps();

            $table->index(['county', 'state']);
            $table->index('fips');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('county_states');
    }
};
