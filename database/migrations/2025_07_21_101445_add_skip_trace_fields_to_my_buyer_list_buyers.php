<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('age')->nullable();
            $table->boolean('deceased')->nullable();
            $table->json('phone_numbers')->nullable();
            $table->json('phone_type')->nullable();
            $table->json('emails')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->json('connected_people')->nullable();
            $table->boolean('skip_traced')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'age', 'deceased',
                'phone_numbers', 'phone_type', 'emails',
                'street', 'city', 'state', 'zip', 'connected_people', 'skip_traced'
            ]);
        });
    }
};