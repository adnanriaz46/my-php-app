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
        Schema::create('request_a_showings', function (Blueprint $table) {
            $table->id();
            $table->string('full_street_address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('property_id');
            $table->dateTime('preferred_time');
            $table->text('message')->nullable();
            $table->unsignedBigInteger('wholesale_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_a_showings');
    }
};
