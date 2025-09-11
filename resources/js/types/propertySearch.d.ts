import {MileSearchAddress} from "@/pages/property-search/subs/filterHelper";

export interface SavedSearchShort {
    id: number | null;
    saved_search_name?: string | null;
    saved_search_type?: string | null;
    default?: boolean | null;
}

export interface SavedSearchFull {
    id: number;
    user_id?: number | null;
    saved_search_name?: string | null;
    saved_search_type?: string | null;

    // Status
    status?: string[] | null;

    // DealType
    deal_type?: string | null;

    // Location
    proximity_object?: MileSearchAddress | null;
    comps_sub_prop_id?: string | null;
    distance_max?: number | null;
    county?: string[] | null;
    city_name_keyword?: string | null;
    zip?: string | null;

    // Gold Filter
    gold_filter_type?: number | null;
    delta_min?: number | null;
    delta_max?: number | null;
    est_profit_min?: number | null;
    est_profit_max?: number | null;
    est_cashflow_min?: number | null;
    est_cashflow_max?: number | null;
    accuracy_score_rent?: number | null;
    accuracy_score_value?: number | null;

    // DOM
    dom_min?: number | null;
    dom_max?: number | null;
    closed_date_min?: string | null; // ISO date string
    closed_date_max?: string | null;

    // More
    list_price_min?: number | null;
    list_price_max?: number | null;
    structure_type?: string[] | null;
    fulladdress_avoid?: string | null;
    total_finished_sqft_min?: number | null;
    total_finished_sqft_max?: number | null;
    lot_sqf_min?: number | null;
    lot_sqf_max?: number | null;
    year_build_min?: number | null;
    year_build_max?: number | null;
    bedrooms_min?: number | null;
    bedrooms_max?: number | null;
    remarks_public_keywords?: string | null;
    list_agent_keyword?: string | null;
    school_district?: string[] | null;
    medianrent_min?: number | null;
    medianrent_max?: number | null;
    est_arv_min?: number | null;
    est_arv_max?: number | null;

    // Order By
    order_by?: string | null;

    // Limit
    _limit?: number | null;

    // Additional
    state_or_province_keyword?: string | null;
    fulladdress_keyword?: string | null;
    mls_number?: string | null;
    listing_entry_date_min?: string | null; // ISO date
    listing_entry_date_max?: string | null;
    user_lat?: number | null;
    user_lng?: number | null;
    test?: string | null;
    city_names_avoid?: string | null;
    filter_ids?: string | null;
    all_wholesale?: boolean | null;
    map_bound_range?: string | null;

    default?: boolean | null
    property_id?: string | null;

    created_at: string;
    updated_at: string;
}

