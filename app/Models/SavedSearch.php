<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $fillable = [
        'user_id',
        'saved_search_name',
        'saved_search_type',
        'status',
        'deal_type',
        'comps_sub_prop_id',
        'distance_max',
        'county',
        'city_name_keyword',
        'zip',
        'delta_min',
        'delta_max',
        'est_profit_min',
        'est_profit_max',
        'est_cashflow_min',
        'est_cashflow_max',
        'accuracy_score_rent',
        'accuracy_score_value',
        'dom_min',
        'dom_max',
        'closed_date_min',
        'closed_date_max',
        'list_price_min',
        'list_price_max',
        'structure_type',
        'fulladdress_avoid',
        'total_finished_sqft_min',
        'total_finished_sqft_max',
        'lot_sqf_min',
        'lot_sqf_max',
        'year_build_min',
        'year_build_max',
        'bedrooms_min',
        'bedrooms_max',
        'remarks_public_keywords',
        'list_agent_keyword',
        'school_district',
        'medianrent_min',
        'medianrent_max',
        'est_arv_min',
        'est_arv_max',
        'order_by',
        '_limit',
        'state_or_province_keyword',
        'fulladdress_keyword',
        'mls_number',
        'listing_entry_date_min',
        'listing_entry_date_max',
        'user_lat',
        'user_lng',
        'test',
        'city_names_avoid',
        'filter_ids',
        'all_wholesale',
        'map_bound_range',
        'property_id',
        'gold_filter_type',
        'proximity_object',
        'default'
    ];

    protected $casts = [
        'default' => 'boolean',
        'status' => 'array',
        'county' => 'array',
        'structure_type' => 'array',
        'school_district' => 'array',
        'all_wholesale' => 'boolean',
        'user_lat' => 'float',
        'user_lng' => 'float',
    ];

    /**
     * Relationship: SavedSearch belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
