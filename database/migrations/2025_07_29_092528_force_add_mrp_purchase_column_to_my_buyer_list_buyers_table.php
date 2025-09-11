<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $table) {
            $table->string('mrp_purchase')->nullable()->after('mrp_sales_price');
        });
    }

    public function down(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $table) {
            $table->dropColumn('mrp_purchase');
        });
    }
};
