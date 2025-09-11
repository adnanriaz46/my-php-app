<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $t) {
            // who triggered a billable/API run (nullable for community views)
            $t->foreignId('last_traced_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // when it was run (timezone aware on Postgres)
            $t->timestampTz('last_traced_at')->nullable();

            // whether contact info (phones/emails) was returned
            $t->boolean('last_trace_success')->nullable();

            // source of data: 'api' (billable) or 'community' (free)
            $t->string('last_trace_source', 20)->nullable();

            // helpful indexes
            $t->index('last_traced_at');
            $t->index('last_trace_success');
            $t->index('last_trace_source');
        });
    }

    public function down(): void
    {
        Schema::table('my_buyer_list_buyers', function (Blueprint $t) {
            // Drop foreign key constraint first
            $t->dropForeign(['last_traced_by_user_id']);
            
            // Drop all columns
            $t->dropColumn([
                'last_traced_by_user_id',
                'last_traced_at',
                'last_trace_success',
                'last_trace_source',
            ]);
        });
    }
};
