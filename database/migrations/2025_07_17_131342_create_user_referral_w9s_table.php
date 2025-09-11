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
        Schema::create('user_referral_w9s', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('file_url');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_referral_w9s');
    }
};
