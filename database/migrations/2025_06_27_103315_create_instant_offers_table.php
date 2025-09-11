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
        Schema::create('instant_offers', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->string('full_street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->unsignedBigInteger('property_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('buyer_name_llc')->nullable();
            $table->decimal('deposit_price', 10)->nullable();
            $table->decimal('offer_price', 10)->nullable();
            $table->date('preferred_closing_date')->nullable();
            $table->text('note')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('agent_email')->nullable();
            $table->decimal('agent_commission', 10)->nullable();
            $table->string('tin')->nullable();
            $table->string('assignor_name')->nullable();
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
        Schema::dropIfExists('instant_offers');
    }
};
