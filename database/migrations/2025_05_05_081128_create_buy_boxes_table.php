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
        Schema::create('buy_boxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->jsonb('investment_strategy')->nullable();
            $table->jsonb('counties_invest')->nullable();
            $table->jsonb('property_types')->nullable();
            $table->decimal('arv_min', 15, 2)->nullable();
            $table->decimal('arv_max', 15, 2)->nullable();
            $table->decimal('bath_min', 15, 2)->nullable();
            $table->decimal('bath_max', 15, 2)->nullable();
            $table->decimal('bed_min', 15, 2)->nullable();
            $table->decimal('bed_max', 15, 2)->nullable();
            $table->decimal('cashflow_min', 15, 2)->nullable();
            $table->decimal('cashflow_max', 15, 2)->nullable();
            $table->decimal('delta_psf_min', 15, 2)->nullable();
            $table->decimal('delta_psf_max', 15, 2)->nullable();
            $table->decimal('flip_profit_min', 15, 2)->nullable();
            $table->decimal('flip_profit_max', 15, 2)->nullable();
            $table->decimal('price_min', 15, 2)->nullable();
            $table->decimal('price_max', 15, 2)->nullable();
            $table->decimal('sqft_min', 15, 2)->nullable();
            $table->decimal('sqft_max', 15, 2)->nullable();
            $table->decimal('year_build_min', 15, 2)->nullable();
            $table->decimal('year_build_max', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_boxes');
    }
};
