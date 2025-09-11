<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavedSearchRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Required identifiers
            'savedSearchName' => ['required', 'string', 'max:255'],
            'savedSearchId' => ['nullable', 'integer', 'max:100'],
            'savedSearchAsNew' => ['required', 'boolean'],

            // Status
            'status' => ['nullable', 'array'],
            'status.*' => ['string'],

            // DealType
            'deal_type' => ['nullable', 'string', 'max:100'],

            // Location
            'proximity_object' => ['nullable', 'array'],
            'proximity_object.address' => ['nullable', 'string'],
            'proximity_object.lat' => ['nullable', 'numeric'],
            'proximity_object.lng' => ['nullable', 'numeric'],
            'comps_sub_prop_id' => ['nullable', 'string'],
            'distance_max' => ['nullable', 'numeric'],
            'county' => ['nullable', 'array'],
            'county.*' => ['string'],
            'city_name_keyword' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'numeric'],

            // Gold Filter (numeric bounds)
            'gold_filter_type' => ['nullable', 'numeric'],
            'delta_min' => ['nullable', 'numeric'],
            'delta_max' => ['nullable', 'numeric'],
            'est_profit_min' => ['nullable', 'numeric'],
            'est_profit_max' => ['nullable', 'numeric'],
            'est_cashflow_min' => ['nullable', 'numeric'],
            'est_cashflow_max' => ['nullable', 'numeric'],
            'accuracy_score_rent' => ['nullable', 'numeric'],
            'accuracy_score_value' => ['nullable', 'numeric'],

            // DOM & dates
            'dom_min' => ['nullable', 'numeric'],
            'dom_max' => ['nullable', 'numeric'],
            'closed_date_min' => ['nullable', 'date'],
            'closed_date_max' => ['nullable', 'date'],

            // More numeric filters
            'list_price_min' => ['nullable', 'numeric'],
            'list_price_max' => ['nullable', 'numeric'],
            'structure_type' => ['nullable', 'array'],
            'structure_type.*' => ['string'],
            'fulladdress_avoid' => ['nullable', 'string'],
            'total_finished_sqft_min' => ['nullable', 'numeric'],
            'total_finished_sqft_max' => ['nullable', 'numeric'],
            'lot_sqf_min' => ['nullable', 'numeric'],
            'lot_sqf_max' => ['nullable', 'numeric'],
            'year_build_min' => ['nullable', 'numeric'],
            'year_build_max' => ['nullable', 'numeric'],
            'bedrooms_min' => ['nullable', 'numeric'],
            'bedrooms_max' => ['nullable', 'numeric'],
            'remarks_public_keywords' => ['nullable', 'string'],
            'list_agent_keyword' => ['nullable', 'string'],
            'school_district' => ['nullable', 'array'],
            'school_district.*' => ['string'],
            'medianrent_min' => ['nullable', 'numeric'],
            'medianrent_max' => ['nullable', 'numeric'],
            'est_arv_min' => ['nullable', 'numeric'],
            'est_arv_max' => ['nullable', 'numeric'],

            // Order & limit
            'order_by' => ['nullable', 'string'],
            '_limit' => ['nullable', 'numeric'],

            // Additional fields
            'state_or_province_keyword' => ['nullable', 'string'],
            'fulladdress_keyword' => ['nullable', 'string'],
            'mls_number' => ['nullable', 'string'],
            'listing_entry_date_min' => ['nullable', 'date'],
            'listing_entry_date_max' => ['nullable', 'date'],
            'user_lat' => ['nullable', 'numeric'],
            'user_lng' => ['nullable', 'numeric'],
            'test' => ['nullable', 'string'],
            'city_names_avoid' => ['nullable', 'string'],
            'all_wholesale' => ['nullable', 'boolean'],
            'map_bound_range' => ['nullable', 'string'],

            // Property ID
            'property_id' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'savedSearchName.required' => 'Please provide valid name for your filters.',
        ];
    }
}
