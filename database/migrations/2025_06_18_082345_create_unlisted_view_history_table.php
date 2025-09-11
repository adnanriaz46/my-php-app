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
        Schema::create('unlisted_view_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zpid')->nullable()->index();
            $table->string('address')->nullable();
            $table->string('platform')->nullable();
            $table->string('useragent')->nullable();
            $table->boolean('is_mobile')->nullable();
            $table->string('ip')->nullable();
            $table->string('ip_city')->nullable();
            $table->string('ip_region')->nullable();
            $table->string('ip_country')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlisted_view_histories');
    }
};
