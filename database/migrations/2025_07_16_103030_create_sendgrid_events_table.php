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
        Schema::create('sendgrid_events', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time')->nullable();
            $table->string('category')->nullable();
            $table->string('email')->nullable();
            $table->string('event')->nullable();
            $table->string('event_id')->nullable();
            $table->boolean('machine_open')->nullable();
            $table->string('message_id_full')->nullable();
            $table->string('message_id')->nullable();
            $table->text('reason')->nullable();
            $table->text('response')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sendgrid_events');
    }
};
