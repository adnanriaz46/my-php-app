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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('email');
            $table->string('name');
            $table->text('profile_image')->nullable();
            $table->timestamp('published_date')->nullable();
            $table->integer('rate')->default(5); // Rating from 1-5
            $table->string('title');
            $table->timestamps();
            
            $table->index(['published_date']);
            $table->index(['rate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
