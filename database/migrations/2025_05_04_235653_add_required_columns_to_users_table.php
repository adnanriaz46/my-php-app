<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');
        Schema::table('users', function (Blueprint $table) {
            // Basic Info
            $table->uuid('uuid')->unique()->index();
            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('street_address')->nullable();
            $table->text('city')->nullable();
            $table->text('state')->nullable();
            $table->text('zip')->nullable();
            $table->text('phone_number')->nullable();
            $table->text('profile_picture')->nullable();
            $table->text('about_me')->nullable();

            // Company Info, Social & Web
            $table->text('company_email')->nullable();
            $table->text('company_logo')->nullable();
            $table->text('company_name')->nullable();
            $table->text('brokerage_name')->nullable();
            $table->text('agent_license_number')->nullable();
            $table->text('website')->nullable();
            $table->text('facebook_url')->nullable();
            $table->text('instagram_url')->nullable();
            $table->text('linkedin_url')->nullable();

            // Role & Preferences
            $table->integer('user_type')->default(2); // non-nullable 1=Admin 2=Free 3=Premium
            $table->boolean('wholesale_enabled')->default(false);
            $table->jsonb('new_user_tour')->nullable(); // List of text
            $table->float('total_rating_sum')->nullable();
            $table->integer('searches_count')->nullable();

            // Affiliate
            $table->boolean('affiliate_eligible')->default(false);
            $table->text('affiliate_slug')->nullable();
            $table->foreignId('affiliated_user')->nullable()->constrained('users')->nullOnDelete();

            // Assessments
            $table->unsignedBigInteger('assessment')->nullable();
            $table->boolean('assessment_filled')->default(false);

            // Email Settings
            $table->boolean('email_verified')->default(false);
            $table->boolean('email_unsubscribed_global')->default(false);
            $table->jsonb('email_unsubscribed_list_preference')->nullable(); // list of IDs

            // IP & Login Tracking
            $table->text('ip_address_text')->nullable(); // single IP
            $table->text('ip_location_lat')->nullable();
            $table->text('ip_location_lng')->nullable();
            $table->jsonb('ip_address')->nullable(); // list of IPs
            $table->timestamp('last_login')->nullable();
            $table->integer('visit_counts')->nullable();

            // Stripe & Subscription
            $table->text('stripe_customer_id')->nullable();
            $table->text('stripe_subscription_id')->nullable();
            $table->jsonb('subscribed_counties')->nullable(); // list of county IDs
            $table->boolean('subscription_period_monthly')->default(false);
            $table->timestamp('subscription_start')->nullable();
            $table->timestamp('subscription_end')->nullable();
            $table->text('subscription_status')->nullable();
        });
        DB::statement('CREATE INDEX IF NOT EXISTS users_name_trgm_idx ON users USING gin (name gin_trgm_ops);');
        DB::statement('CREATE INDEX IF NOT EXISTS  users_email_trgm_idx ON users USING gin (email gin_trgm_ops);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'street_address',
                'city',
                'state',
                'zip',
                'phone_number',
                'profile_picture',
                'about_me',
                'company_email',
                'company_logo',
                'company_name',
                'brokerage_name',
                'agent_license_number',
                'website',
                'facebook_url',
                'instagram_url',
                'linkedin_url',
                'user_type',
                'wholesale_enabled',
                'new_user_tour',
                'total_rating_sum',
                'searches_count',
                'affiliate_eligible',
                'affiliate_slug',
                'affiliated_user',
                'assessment',
                'assessment_filled',
                'email_verified',
                'email_unsubscribed_global',
                'email_unsubscribed_list_preference',
                'ip_address_text',
                'ip_address',
                'ip_location_lat',
                'ip_location_lng',
                'last_login',
                'visit_counts',
                'stripe_customer_id',
                'stripe_subscription_id',
                'subscribed_counties',
                'subscription_period_monthly',
                'subscription_start',
                'subscription_end',
                'subscription_status'
            ]);
        });
    }
};
