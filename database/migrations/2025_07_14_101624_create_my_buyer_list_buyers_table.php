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
        Schema::create('my_buyer_list_buyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('my_buyer_list_id');
            $table->string('investor_identifier');
            $table->string('mrp_fullstreet')->nullable();
            $table->string('mrp_city')->nullable();
            $table->string('mrp_state')->nullable();
            $table->string('mrp_zip')->nullable();
            $table->string('mrp_sales_price')->nullable();
            $table->string('mrp_beds')->nullable();
            $table->string('mrp_bath')->nullable();
            $table->string('mrp_sqft')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_buyer_list_buyers');
    }
};
