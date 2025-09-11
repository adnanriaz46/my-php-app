<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mls_offer_pdf_templates', function (Blueprint $table) {
            $table->id();
            $table->text('additional_requests')->nullable();
            $table->boolean('fillable')->default(false);
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->string('folder_name')->nullable();
            $table->unsignedBigInteger('template_id');
            $table->string('template_name');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mls_offer_pdf_templates');
    }
}; 