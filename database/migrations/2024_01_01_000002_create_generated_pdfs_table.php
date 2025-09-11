<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained('pdf_templates')->onDelete('cascade');
            $table->json('data_json');
            $table->string('pdf_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_pdfs');
    }
}; 