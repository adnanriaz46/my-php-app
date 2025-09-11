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
        Schema::create('wholesale_properties', function (Blueprint $table) {
            $table->id();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->decimal('baths', 3, 1)->nullable();
            $table->decimal('beds', 3, 1)->nullable();
            $table->string('structure_type')->nullable();
            $table->string('total_finished_sqft')->nullable();
            $table->string('lot_sqft')->nullable();
            $table->integer('year_built')->nullable();
            $table->integer('building_units_total')->nullable();
            $table->string('cool_type')->nullable();
            $table->string('heat_type')->nullable();

            $table->string('tax_annual_amount')->nullable();
            $table->string('tax_assessed_value')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->string('zoning')->nullable();

            $table->string('full_street_address')->nullable();
            $table->string('city_name')->nullable();
            $table->string('state_or_province')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('geo_address')->nullable();
            $table->string('county')->nullable();

            $table->string('status')->nullable();
            $table->integer('list_price')->nullable();
            $table->dateTime('closed_date')->nullable();
            $table->integer('closed_price')->nullable();

            $table->jsonb('images')->nullable(); // images
            $table->boolean('hoa')->nullable();
            $table->string('listing_agent')->nullable();
            $table->string('listing_office')->nullable();
            $table->string('listing_agent_email')->nullable();
            $table->string('school_district')->nullable();
            $table->string('municipality')->nullable();
            $table->text('remarks_public')->nullable();

            $table->decimal('rental_estimate', 10)->nullable();
            $table->decimal('seller_arv', 10)->nullable();
            $table->decimal('seller_avg_rent', 10)->nullable();
            $table->decimal('seller_avm', 10)->nullable();
            $table->decimal('seller_est_cashflow', 10)->nullable();
            $table->decimal('seller_est_flip_profit', 10)->nullable();
            $table->decimal('seller_est_flip_rehab', 10)->nullable();
            $table->decimal('seller_est_rental_rehab', 10)->nullable();

            $table->integer('database_id')->nullable()->index();

            $table->boolean('show_full_address')->nullable();
            $table->boolean('manual_address')->nullable();
            $table->boolean('approved')->nullable();
            $table->string('slug')->nullable();
            $table->foreignId('user_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_properties');
    }
};
