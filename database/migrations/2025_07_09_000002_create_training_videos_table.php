<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_post_id')->constrained('training_posts')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->string('topic')->nullable();
            $table->text('summary')->nullable();
            $table->text('summary_html')->nullable();
            $table->text('embed_code')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->string('url')->nullable();
            $table->text('transcript')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('training_videos');
    }
}; 