<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_tag_ownership', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->string('tag_name');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['contact_id', 'tag_name', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_tag_ownership');
    }
};