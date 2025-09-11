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
        Schema::create('mls_offer_generated_logs', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->text('file_url');
            $table->text('file_name');
            $table->boolean('is_email')->default(false);
            $table->text('email_error')->nullable();
            $table->boolean('email_success')->default(false);
            $table->unsignedBigInteger('email_template_id');
            $table->text('offer_description')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->jsonb('property_data')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->dateTime('send_at')->nullable();
            $table->text('sent_to')->nullable();
            $table->text('subject');
            $table->unsignedBigInteger('pdf_template_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mls_offer_generated_logs');
    }
};
