<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFilterHistory extends Model
{
    protected $fillable = [
        'delta_min',
        'delta_max',
        'est_profit_min',
        'est_profit_max',
        'est_cashflow_min',
        'est_cashflow_max',
        'remarks_public_keywords',
        'city_name_keyword',
        'dom_min',
        'dom_max',
        'est_arv_min',
        'est_arv_max',
        'lot_sqf_min',
        'lot_sqf_max',
        'total_finished_sqft_min',
        'total_finished_sqft_max',
        'list_price_min',
        'list_price_max',
        'medianrent_min',
        'medianrent_max',
        'state_or_province_keyword',
        'bedrooms_min',
        'bedrooms_max',
        'county',
        'status',
        'list_agent_keyword',
        'fulladdress_keyword',
        'structure_type',
        'mls_number',
        'listing_entry_date_min',
        'listing_entry_date_max',
        'closed_date_min',
        'closed_date_max',
        'order_by',
        '_limit',
        'zip',
        'comps_sub_prop_id',
        'distance_max',
        'user_lat',
        'user_lng',
        'test',
        'deal_type',
        'year_built_min',
        'year_built_max',
        'city_names_avoid',
        'filter_ids',
        'fulladdress_avoid',
        'all_wholesale',
        'accuracy_score_value',
        'accuracy_score_rent',
        'school_district_name',
        'is_mobile',
        'platform',
        'useragent',
        'ip',
        'ip_city',
        'ip_region',
        'ip_country',
        'user_id',
    ];

    protected $casts = [
        'is_mobile' => 'boolean',
        'all_wholesale' => 'boolean',
        'state_or_province_keyword' => 'array',
        'county' => 'array',
        'status' => 'array',
        'structure_type' => 'array',
        'city_names_avoid' => 'array',
        'filter_ids' => 'array',
        'school_district_name' => 'array',
        'listing_entry_date_min' => 'date',
        'listing_entry_date_max' => 'date',
        'closed_date_min' => 'date',
        'closed_date_max' => 'date',
    ];

    /**
     * Get the user that owns the property filter history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
