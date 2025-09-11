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
        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->index();
                $table->string('phone')->nullable();
                $table->jsonb('price_range')->nullable();
                $table->jsonb('tags')->nullable()->index();
                $table->jsonb('counties')->nullable()->index();
                $table->jsonb('zip')->nullable()->index();
                $table->jsonb('deal_type')->nullable()->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('email_type')->default('regular');
                $table->text('first_name')->nullable();
                $table->text('last_name')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
