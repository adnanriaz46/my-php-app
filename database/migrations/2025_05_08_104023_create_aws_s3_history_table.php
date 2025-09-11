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
        Schema::create('aws_s3_histories', function (Blueprint $table) {
            $table->id();
            $table->text('type');
            $table->text('description')->nullable();
            $table->text('path')->nullable();
            $table->text('url')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->text('errors')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aws_s3_histories');
    }
};
