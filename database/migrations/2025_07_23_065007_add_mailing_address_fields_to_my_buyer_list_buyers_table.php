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
        Schema::table('my_buyer_list_buyers', function (Blueprint $table) {
            $table->string('MailingFullStreetAddress')->nullable();
            $table->string('MailingCity')->nullable();
            $table->string('MailingState')->nullable();
            $table->string('MailingZIP5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $table) {
            $table->dropColumn([
                'MailingFullStreetAddress',
                'MailingCity', 
                'MailingState',
                'MailingZIP5'
            ]);
        });
    }
};
