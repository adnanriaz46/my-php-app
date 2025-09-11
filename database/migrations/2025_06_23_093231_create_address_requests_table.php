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
        Schema::create('address_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('is_agreed')->default(0); // values will be 0,1,2 0 =pending, 1=approved, 2= rejected
            $table->unsignedBigInteger('property_id');
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('address_requests');
    }
};
