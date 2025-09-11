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
        // Drop the existing table if it exists
        Schema::dropIfExists('my_buyer_list_buyers');

        // Create the table with the exact columns from the image
        Schema::create('my_buyer_list_buyers', function (Blueprint $table) {
            $table->id(); // 1. id
            $table->unsignedBigInteger('my_buyer_list_id'); // 2. my_buyer_list_id
            $table->string('investor_identifier'); // 3. investor_identifier
            $table->string('mrp_fullstreet')->nullable(); // 4. mrp_fullstreet
            $table->string('mrp_city')->nullable(); // 5. mrp_city
            $table->string('mrp_state')->nullable(); // 6. mrp_state
            $table->string('mrp_zip')->nullable(); // 7. mrp_zip
            $table->string('mrp_sales_price')->nullable(); // 8. mrp_sales_price
            $table->string('mrp_beds')->nullable(); // 9. mrp_beds
            $table->string('mrp_bath')->nullable(); // 10. mrp_bath
            $table->string('mrp_sqft')->nullable(); // 11. mrp_sqft
            $table->timestamps(); // 12. created_at, 13. updated_at
            $table->string('MailingFullStreetAddress')->nullable(); // 14. MailingFullStreetAddress
            $table->string('MailingCity')->nullable(); // 15. MailingCity
            $table->string('MailingState')->nullable(); // 16. MailingState
            $table->string('MailingZIP5')->nullable(); // 17. MailingZIP5
            $table->unsignedBigInteger('skip_trace_result_id')->nullable(); // 18. skip_trace_result_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_buyer_list_buyers');
    }
};
