<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'phone',
        'user_identifier',
        'investment_strategy',
        'counties_invest',
        'how_long_in_rei',
        'how_many_agent_deals',
        'how_many_flips',
        'how_many_land',
        'how_many_new_construction',
        'how_many_private_lending',
        'how_many_rentals',
        'how_many_wholesale',
        'other_investments_yn',
        'other_investments_detail',
        'primary_goal',
        'investment_biz_plan',
        'smart_goals',
        'main_obstacle',
        'acq_strategy',
        'time_per_week_actual',
        'time_per_week_goal',
        'pillar_ranking_finance',
        'pillar_ranking_leadership',
        'pillar_ranking_marketing',
        'pillar_ranking_operations',
        'pillar_ranking_sales',
        'average_deal_profit',
        'what_would_you_pay',
        'want_auto_offers',
        'additional_comments',
        'assessment_completed',
        'drip_mail_completed_step',
        'drip_mail_sent_at',
        'assessment_overview_response',
        'slug',
        'creator',
        'unique_id',
    ];

    protected $casts = [
        'investment_strategy' => 'array',
        'counties_invest' => 'array',
        'acq_strategy' => 'array',
        'other_investments_yn' => 'boolean',
        'want_auto_offers' => 'boolean',
        'assessment_completed' => 'boolean',
        'how_many_agent_deals' => 'integer',
        'how_many_flips' => 'integer',
        'how_many_land' => 'integer',
        'how_many_new_construction' => 'integer',
        'how_many_private_lending' => 'integer',
        'how_many_rentals' => 'integer',
        'how_many_wholesale' => 'integer',
        'pillar_ranking_finance' => 'integer',
        'pillar_ranking_leadership' => 'integer',
        'pillar_ranking_marketing' => 'integer',
        'pillar_ranking_operations' => 'integer',
        'pillar_ranking_sales' => 'integer',
        'drip_mail_completed_step' => 'integer',
        'average_deal_profit' => 'decimal:2',
        'what_would_you_pay' => 'decimal:2',
        'drip_mail_sent_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the drip emails for this assessment response.
     */
    public function dripEmails(): HasMany
    {
        return $this->hasMany(AssessmentDripEmail::class);
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the total number of deals completed.
     */
    public function getTotalDealsAttribute(): int
    {
        return $this->how_many_agent_deals +
               $this->how_many_flips +
               $this->how_many_land +
               $this->how_many_new_construction +
               $this->how_many_private_lending +
               $this->how_many_rentals +
               $this->how_many_wholesale;
    }

    /**
     * Check if the assessment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->assessment_completed;
    }

    /**
     * Get the current step of the assessment.
     */
    public function getCurrentStep(): int
    {
        if ($this->assessment_completed) {
            return 19; // Final step
        }
        
        // Logic to determine current step based on filled fields
        if (empty($this->email)) return 1;
        if (empty($this->investment_strategy)) return 2;
        if (empty($this->counties_invest)) return 3;
        if (empty($this->how_long_in_rei)) return 4;
        if (empty($this->how_many_agent_deals) && empty($this->how_many_flips) && 
            empty($this->how_many_land) && empty($this->how_many_new_construction) && 
            empty($this->how_many_private_lending) && empty($this->how_many_rentals) && 
            empty($this->how_many_wholesale)) return 5;
        if (empty($this->primary_goal)) return 7;
        if (empty($this->investment_biz_plan)) return 8;
        if (empty($this->smart_goals)) return 9;
        if (empty($this->main_obstacle)) return 10;
        if (empty($this->acq_strategy)) return 11;
        if (empty($this->time_per_week_actual)) return 12;
        if (empty($this->time_per_week_goal)) return 13;
        if (empty($this->pillar_ranking_finance) || empty($this->pillar_ranking_leadership) || 
            empty($this->pillar_ranking_marketing) || empty($this->pillar_ranking_operations) || 
            empty($this->pillar_ranking_sales)) return 14;
        if (empty($this->average_deal_profit)) return 15;
        if (empty($this->what_would_you_pay)) return 16;
        if (empty($this->want_auto_offers)) return 17;
        if (empty($this->additional_comments)) return 18;
        
        return 19; // Ready for completion
    }

    /**
     * Scope to get completed assessments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('assessment_completed', true);
    }

    /**
     * Scope to get incomplete assessments.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('assessment_completed', false);
    }

    /**
     * Scope to get assessments by email.
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
