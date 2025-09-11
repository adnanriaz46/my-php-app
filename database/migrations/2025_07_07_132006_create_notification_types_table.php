<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // property, user-account, site-updates, buyers, etc.
            $table->string('display_name'); // Human readable name
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon class or identifier
            $table->string('color')->nullable(); // Color theme for this type
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default notification types
        DB::table('notification_types')->insert([
            [
                'name' => 'property',
                'display_name' => 'Property Updates',
                'description' => 'Notifications related to property listings, updates, and changes',
                'icon' => 'tabler:home',
                'color' => 'blue',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user-account',
                'display_name' => 'Account Updates',
                'description' => 'Notifications related to user account changes, settings, and profile updates',
                'icon' => 'tabler:user',
                'color' => 'green',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'site-updates',
                'display_name' => 'Site Updates',
                'description' => 'System notifications, maintenance updates, and platform changes',
                'icon' => 'tabler:settings',
                'color' => 'purple',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'buyers',
                'display_name' => 'Buyer Activity',
                'description' => 'Notifications related to buyer inquiries, offers, and interactions',
                'icon' => 'tabler:users',
                'color' => 'orange',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'email-marketing',
                'display_name' => 'Email Marketing',
                'description' => 'Notifications related to email campaigns, delivery status, and analytics',
                'icon' => 'tabler:mail',
                'color' => 'indigo',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'subscription',
                'display_name' => 'Subscription',
                'description' => 'Notifications related to subscription changes, payments, and billing',
                'icon' => 'tabler:credit-card',
                'color' => 'red',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_types');
    }
};
