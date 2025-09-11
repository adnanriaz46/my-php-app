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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('saved_search_name');
            $table->string('saved_search_type');

            // Status
            $table->json('status')->nullable();

            // DealType
            $table->string('deal_type')->nullable();

            // Location
            $table->string('comps_sub_prop_id')->nullable();
            $table->decimal('distance_max')->nullable();
            $table->json('county')->nullable();
            $table->string('city_name_keyword')->nullable();
            $table->string('zip')->nullable();

            // Gold Filter
            $table->integer('gold_filter_type')->nullable();
            $table->decimal('delta_min', 10, 2)->nullable();
            $table->decimal('delta_max', 10, 2)->nullable();
            $table->decimal('est_profit_min', 12, 2)->nullable();
            $table->decimal('est_profit_max', 12, 2)->nullable();
            $table->decimal('est_cashflow_min', 12, 2)->nullable();
            $table->decimal('est_cashflow_max', 12, 2)->nullable();
            $table->decimal('accuracy_score_rent', 5, 2)->nullable();
            $table->decimal('accuracy_score_value', 5, 2)->nullable();

            // DOM
            $table->decimal('dom_min')->nullable();
            $table->decimal('dom_max')->nullable();
            $table->date('closed_date_min')->nullable();
            $table->date('closed_date_max')->nullable();

            // More
            $table->decimal('list_price_min', 12, 2)->nullable();
            $table->decimal('list_price_max', 12, 2)->nullable();
            $table->json('structure_type')->nullable();
            $table->string('fulladdress_avoid')->nullable();
            $table->string('property_id')->nullable(); // id = property_id
            $table->decimal('total_finished_sqft_min', 10, 2)->nullable();
            $table->decimal('total_finished_sqft_max', 10, 2)->nullable();
            $table->decimal('lot_sqf_min', 10, 2)->nullable();
            $table->decimal('lot_sqf_max', 10, 2)->nullable();
            $table->year('year_build_min')->nullable();
            $table->year('year_build_max')->nullable();
            $table->decimal('bedrooms_min')->nullable();
            $table->decimal('bedrooms_max')->nullable();
            $table->text('remarks_public_keywords')->nullable();
            $table->string('list_agent_keyword')->nullable();
            $table->json('school_district')->nullable();
            $table->decimal('medianrent_min', 12, 2)->nullable();
            $table->decimal('medianrent_max', 12, 2)->nullable();
            $table->decimal('est_arv_min', 12, 2)->nullable();
            $table->decimal('est_arv_max', 12, 2)->nullable();

            // Order By
            $table->string('order_by')->nullable();

            // Limit
            $table->decimal('_limit')->nullable();

            // Additional
            $table->string('state_or_province_keyword')->nullable();
            $table->string('fulladdress_keyword')->nullable();
            $table->string('mls_number')->nullable();
            $table->string('listing_entry_date_min')->nullable();
            $table->string('listing_entry_date_max')->nullable();
            $table->decimal('user_lat', 10, 7)->nullable();
            $table->decimal('user_lng', 10, 7)->nullable();
            $table->string('test')->nullable();
            $table->string('city_names_avoid')->nullable();
            $table->string('filter_ids')->nullable();
            $table->boolean('all_wholesale')->nullable();
            $table->text('map_bound_range')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
