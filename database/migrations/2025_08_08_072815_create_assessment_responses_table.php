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
        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            
            // User Information (Step 1)
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('user_identifier'); // maps to 1_user
            
            // Investment Strategy (Step 2)
            $table->jsonb('investment_strategy')->nullable();
            
            // Counties to Invest (Step 3)
            $table->jsonb('counties_invest')->nullable();
            
            // Experience (Step 4)
            $table->string('how_long_in_rei')->nullable();
            
            // Deal Counts (Step 5)
            $table->integer('how_many_agent_deals')->default(0);
            $table->integer('how_many_flips')->default(0);
            $table->integer('how_many_land')->default(0);
            $table->integer('how_many_new_construction')->default(0);
            $table->integer('how_many_private_lending')->default(0);
            $table->integer('how_many_rentals')->default(0);
            $table->integer('how_many_wholesale')->default(0);
            
            // Other Investments (Step 6)
            $table->boolean('other_investments_yn')->default(false);
            $table->text('other_investments_detail')->nullable();
            
            // Goals and Plans (Steps 7-9)
            $table->text('primary_goal')->nullable();
            $table->text('investment_biz_plan')->nullable();
            $table->text('smart_goals')->nullable();
            
            // Obstacles and Strategy (Steps 10-11)
            $table->text('main_obstacle')->nullable();
            $table->jsonb('acq_strategy')->nullable();
            
            // Time Management (Steps 12-13)
            $table->string('time_per_week_actual')->nullable();
            $table->string('time_per_week_goal')->nullable();
            
            // Pillar Rankings (Step 14)
            $table->integer('pillar_ranking_finance')->nullable();
            $table->integer('pillar_ranking_leadership')->nullable();
            $table->integer('pillar_ranking_marketing')->nullable();
            $table->integer('pillar_ranking_operations')->nullable();
            $table->integer('pillar_ranking_sales')->nullable();
            
            // Financial Information (Steps 15-16)
            $table->decimal('average_deal_profit', 10, 2)->nullable();
            $table->decimal('what_would_you_pay', 10, 2)->nullable();
            
            // Preferences (Step 17)
            $table->boolean('want_auto_offers')->default(false);
            
            // Additional Comments (Step 18)
            $table->text('additional_comments')->nullable();
            
            // Assessment Status
            $table->boolean('assessment_completed')->default(false);
            $table->integer('drip_mail_completed_step')->default(0);
            $table->timestamp('drip_mail_sent_at')->nullable();
            
            // Assessment Overview Response
            $table->text('assessment_overview_response')->nullable();
            
            // Legacy fields from bubble.io
            $table->string('slug')->nullable();
            $table->string('creator')->nullable();
            $table->string('unique_id')->nullable(); // maps to "unique id"
            
            $table->timestamps();
            
            // Indexes
            $table->index('email');
            $table->index('assessment_completed');
            $table->index('drip_mail_completed_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_responses');
    }
};
