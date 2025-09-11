<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_filter_histories', function (Blueprint $table) {
            $table->id();

            // Numeric Ranges
            $table->float('delta_min')->nullable();
            $table->float('delta_max')->nullable();
            $table->float('est_profit_min')->nullable();
            $table->float('est_profit_max')->nullable();
            $table->float('est_cashflow_min')->nullable();
            $table->float('est_cashflow_max')->nullable();
            $table->integer('dom_min')->nullable();
            $table->integer('dom_max')->nullable();
            $table->float('est_arv_min')->nullable();
            $table->float('est_arv_max')->nullable();
            $table->float('lot_sqf_min')->nullable();
            $table->float('lot_sqf_max')->nullable();
            $table->float('total_finished_sqft_min')->nullable();
            $table->float('total_finished_sqft_max')->nullable();
            $table->float('list_price_min')->nullable();
            $table->float('list_price_max')->nullable();
            $table->float('medianrent_min')->nullable();
            $table->float('medianrent_max')->nullable();
            $table->integer('bedrooms_min')->nullable();
            $table->integer('bedrooms_max')->nullable();
            $table->integer('year_built_min')->nullable();
            $table->integer('year_built_max')->nullable();

            // Text Filters
            $table->text('remarks_public_keywords')->nullable();
            $table->string('city_name_keyword')->nullable();
            $table->string('state_or_province_keyword')->nullable(); // JSON-encoded array
            $table->string('county')->nullable(); // JSON-encoded array
            $table->string('status')->nullable(); // JSON-encoded array
            $table->string('structure_type')->nullable(); // JSON-encoded array
            $table->string('list_agent_keyword')->nullable();
            $table->string('fulladdress_keyword')->nullable();
            $table->string('mls_number')->nullable();
            $table->string('zip')->nullable();
            $table->string('comps_sub_prop_id')->nullable();
            $table->text('city_names_avoid')->nullable(); // JSON-encoded array
            $table->text('filter_ids')->nullable(); // JSON-encoded array
            $table->string('fulladdress_avoid')->nullable();
            $table->string('deal_type')->nullable();
            $table->text('school_district_name')->nullable(); // JSON-encoded array

            // Dates
            $table->date('listing_entry_date_min')->nullable();
            $table->date('listing_entry_date_max')->nullable();
            $table->date('closed_date_min')->nullable();
            $table->date('closed_date_max')->nullable();

            // Ordering, pagination, location
            $table->string('order_by')->nullable();
            $table->integer('_limit')->nullable();
            $table->float('distance_max')->nullable();
            $table->float('user_lat')->nullable();
            $table->float('user_lng')->nullable();

            // Misc
            $table->float('test')->nullable();
            $table->boolean('all_wholesale')->nullable();
            $table->float('accuracy_score_value')->nullable();
            $table->float('accuracy_score_rent')->nullable();

            // Metadata
            $table->boolean('is_mobile')->default(false);
            $table->string('platform')->nullable();
            $table->string('useragent')->nullable();
            $table->string('ip')->nullable();
            $table->string('ip_city')->nullable();
            $table->string('ip_region')->nullable();
            $table->string('ip_country')->nullable();

            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_filter_histories');
    }
};
