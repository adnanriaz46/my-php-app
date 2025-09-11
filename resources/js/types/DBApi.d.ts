export interface DBApi<T> {
    error?: string | null; // temp - variable
    status?: number;
    message?: string;
    data?: T;
    count?: number;
}


// DB API Property Count
export interface DBApiPropertyCount {
    count: number,
    user_distance: number
}

// DB API PropertyList
export interface DBApiPropertyList {
    id: number;
    status: string;
    wholesale: string;
    list_price: number;
    office_info: string;
    address: string;
    bedrooms_count: number;
    bathrooms_total_count: number;
    total_finished_sqft: number;
    structure_type: string;
    medianrent: number;
    est_avm: number;
    est_arv: number;
    est_cashflow: number;
    delta_psf: number;
    est_profit: number;
    latitude: number;
    longitude: number;
    image: string;
    real_dom: number;
    close_date: string;
    buyer_financing?: null;
    dom: number;
    mls_number: string;
    low_comps_ppsf: number;
    high_comps_ppsf: number;
    listing_entry_date: string;
    price_per_sqft: number;
    close_price: number;
    price_per_sqft_closed: number;
    list_agent_email: string;
    seller_avm: number;
    seller_arv: number;
    seller_est_flip_rehab: number;
    seller_est_flip_profit: number;
    seller_est_rental_rehab: number;
    seller_est_cashflow: number;
    seller_avg_rent: number;
    accuracy_score_value: number;
    accuracy_score_rent: number;
    short_list_price: string;
    county_state: string;
    mile_range_from_subject: number;
    lot_sqft: number;
    year_built: number;
    school_district_name: string;
}

export interface DBApiPropertyMinimal {
    id: number;
    status: string;
    geo_address: string;
    image?: string | null;
}
export interface DBApiBuyer {
    investor_identifier: string;
    MailingFullStreetAddress: string;
    MailingCity: string;
    MailingState: string;
    MailingZIP5: string;
    properties_owned: string;
    mrp_purchase: string;
    mrp_sales_price: string;
    mrp_fullstreet: string;
    mrp_city: string;
    mrp_state: string;
    mrp_zip: string;
    mrp_beds: string;
    mrp_bath: string;
    mrp_sqft: string;
    mrp_type_class: string;
    mrp_lat: string;
    mrp_lng: string;
    absentee_count: string;
    is_llc: string;
    folsom_properties: string;
    cash_purchase_count: string;
    likelihood_score: string;
    FIPS: string;
    most_likely_buyer_tag: string;
    // Skip trace fields
    skip_traced?: boolean;
    owner_name?: string;
    owner_age?: number;
    owner_phone?: string;
    owner_email?: string;
    owner_deceased?: boolean;
    // Contact fields
    contact_tags?: string[];
    contact_id?: number | null;
    skip_trace_has_contact_info?: boolean;
}
// DB API Property
export interface DBApiPropertyFull {
    id: number;
    tax_id_number: string;
    dom: number;
    full_street_address: string;
    city_name: string;
    state_or_province: string;
    zip_code: string;
    mls_number: string;
    zoning: string;
    status: string;
    county: string;
    bedrooms_count: number;
    bathrooms_total_count: number;
    structure_type: string;
    close_date: string;
    buyer_financing: string;
    list_office_name: string;
    list_agent_first_name: string;
    list_agent_last_name: string;
    list_agent_email: string;
    list_agent_cell: string;
    list_price: number;
    close_price: number;
    total_finished_sqft: number;
    price_per_sqft: number;
    lot_sqft: number;
    improvement_assessed_value: number;
    land_assessed_value: number;
    tax_assessed_value: number;
    tax_annual_amount: number;
    heating_type: string;
    cooling_type: string;
    remarks_public: string;
    lowrent: number;
    comparablerentals: number;
    medianrent: number;
    percentile25: number;
    percentile75: number;
    highrent: number;
    avg_list: number;
    avg_c_price: number;
    avg_p_sqft: number;
    avg_dom: number;
    avg_sqft: number;
    sales: number;
    high_comps_cluster: number;
    high_comps_ppsf: number;
    low_comps_cluster: number;
    low_comps_ppsf: number;
    est_avm: number;
    est_arv: number;
    delta_psf: number;
    est_full_rehab: number;
    est_profit: number;
    est_cashflow: number;
    full_location: string;
    modification_timestamp: string;
    status_changed?: null;
    listing_entry_date: string;
    building_units_total: number;
    year_built: number;
    is_calculated: string;
    added_to_sql_timestamp: string;
    unique_id?: null;
    price_per_sqft_closed: number;
    geo_address: string;
    accuracy_score_value: number;
    accuracy_score_rent: number;
    synced_at: string;
    latitude: number;
    longitude: number;
    bubble_sync: string;
    hoa_yn: string;
    hoa_fee: number;
    hoa_fee_freq: string;
    hoa_name: string;
    land_lease_yn: string;
    land_lease_amount: number;
    land_lease_amt_freq: string;
    ground_rent_exists_yn: string;
    ground_rent_amt: number;
    ground_rent_pymnt_freq: string;
    remarks_agent_private: string;
    municipality: string;
    elementary_school: string;
    high_school: string;
    middle_or_junio_school: string;
    school_district_name: string;
    list_agent_state_license_number: string;
    is_avg_calculated: string;
    wholesale: string;
    wholesale_id?: null;
    seller_avm: number;
    seller_arv: number;
    seller_avg_rent: number;
    seller_est_rental_rehab: number;
    seller_est_flip_rehab: number;
    short_list_price: string;
    seller_est_flip_profit: number;
    seller_est_cashflow: number;
    clean_address: string;
    ignore_zillow: string;
    mls_modification_at: string;
    hot_water: string;
    water_source: string;
    sewer_septic: string;
    wholesale_mail_sent: string;
    real_dom: string;
    mile_range_from_subject: number;
}

// DB API [GetPropertyParams]
export interface paramsDBApiGetProperty {
    delta_min?: number | null,
    delta_max?: number | null,
    est_profit_min?: number | null,
    est_profit_max?: number | null,
    est_cashflow_min?: number | null,
    est_cashflow_max?: number | null,
    remarks_public_keywords?: string | null,
    city_name_keyword?: string | null,
    dom_min?: number | null,
    dom_max?: number | null,
    est_arv_min?: number | null,
    est_arv_max?: number | null,
    lot_sqf_min?: number | null,
    lot_sqf_max?: number | null,
    total_finished_sqft_min?: number | null,
    total_finished_sqft_max?: number | null,
    list_price_min?: number | null,
    list_price_max?: number | null,
    medianrent_min?: number | null,
    medianrent_max?: number | null,
    state_or_province_keyword?: string[] | null,
    bedrooms_min?: number | null,
    bedrooms_max?: number | null,
    bathrooms_min?: number | null,
    bathrooms_max?: number | null,
    county?: string[] | null,
    status?: string[] | null,
    list_agent_keyword?: string | null,
    fulladdress_keyword?: string | null,
    structure_type?: string[] | null,
    mls_number?: string | null,
    id?: number | null,
    listing_entry_date_min?: string | null,
    listing_entry_date_max?: string | null,
    closed_date_min?: string | null,
    closed_date_max?: string | null,
    order_by?: string | null,
    _limit?: number | null,
    zip?: string | null,
    comps_sub_prop_id?: string | null,
    distance_max?: number | null,
    user_lat?: number | null,
    user_lng?: number | null,
    test?: number | null,
    deal_type?: string | null,
    year_built_min?: number | null,
    year_built_max?: number | null,
    city_names_avoid?: string[] | null,
    filter_ids?: number[] | null,
    fulladdress_avoid?: string | null,
    all_wholesale?: number | null,
    accuracy_score_value?: number | null,
    accuracy_score_rent?: number | null,
    school_district_name?: string[] | null,
    suppress_ids?: number[] | null,
    ignore_default_user_suppress?: number
}

// DB API COMPS Property
export interface DBApiAverageCompsProperty {
    avg_sales: string | number;
    avg_ppsf: string | number;
    avg_closed_ppsf: string | number;
    avg_sqft: string | number;
    avg_dom: string | number;
    avg_dist: string | number;
    avg_beds: string | number;
    avg_baths: string | number;
    avg_lot_size: string | number;
    count: string | number;
}

// DB API MLS History
export interface DBApiMlsHistory {
    id: string;
    mls_number: string;
    geo_address: string;
    tax_id_number: string;
    listing_entry_date: string;
    status: string;
    close_date: string;
    close_price: string;
    list_price: string;
}


// DB API Calculate Values
export interface DBApiCalValData {
    average_data: DBApiCalValDataAverageData;
    subject_data: DBApiCalValDataSubjectData;
    structure_type?: (string)[] | null;
    active_listing: DBApiCalValDataActiveListing;
}

export interface DBApiCalValDataAverageData {
    arv: number;
    avm: number;
    price_per_sqft: number;
    sales: number;
    delta_psf: number;
    dom: number;
    high_comps_cluster: number;
    low_comps_cluster: number;
    high_comp_ppsf: number;
    low_comp_ppsf: number;
    non_conforming_sales: number;
    sqft: number;
    accuracy_score: number;
}

export interface DBApiCalValDataSubjectData {
    price_per_sqft: number;
    full_rehab: number;
}

export interface DBApiCalValDataActiveListing {
    id: string;
    list_price: string;
    listing_entry_date: string;
}

export interface DBApiOwnershipData {
    PropertyID: string;
    OwnerNAME1FULL: string;
    OwnerNAME2FULL: string;
    MailingFullStreetAddress: string;
    MailingCity: string;
    MailingState: string;
    MailingZIP5: string;
    ConcurrentMtg1LoanAmt: string;
    ConcurrentMtg1Lender: string;
    ConcurrentMtg1Term: string;
    ConcurrentMtg2LoanAmt: string;
    ConcurrentMtg2Lender: string;
    ConcurrentMtg2Term: string;
    SitusFullStreetAddress: string;
    SitusCity: string;
    SitusState: string;
    SitusZIP5: string;
}
export interface DBApiDatatreePropertyInfo {
    PropertyID: string;
    SitusFullStreetAddress: string;
    SitusCity: string;
    SitusState: string;
    SitusZIP5: string;
    TaxAccountNumber: string;
    Zoning: string;
    LotSizeAcres_int: string;
    LotSizeSqFt: string;
    SumLivingAreaSqFt: string;
    Bedrooms: string;
    BathTotalCalc: string;
    YearBuilt: string;
    HOA1Name: string;
    TaxAmt: string;
    AssdTotalValue: string;
    SchoolDistrictName: string;
    Municipality: string;
    CurrentAVMValue: string;
}

export interface DBApiSoldUnderMarketValue {
    id: number;
    status: string;
    wholesale: string;
    list_price: number;
    office_info: string;
    address: string;
    bedrooms_count: number;
    bathrooms_total_count: number;
    total_finished_sqft: number;
    structure_type: string;
    medianrent: number;
    est_avm: number;
    est_arv: number;
    est_cashflow: number;
    delta_psf: number;
    est_profit: number;
    latitude: number;
    longitude: number;
    image: string;
    real_dom: number;
    close_date: string;
    buyer_financing: string;
    dom: number;
    mls_number: string;
    low_comps_ppsf: number;
    high_comps_ppsf: number;
    listing_entry_date: string;
    price_per_sqft: number;
    close_price: number;
    price_per_sqft_closed: number;
    list_agent_email: string;
    seller_avm: number;
    seller_arv: number;
    seller_est_flip_rehab: number;
    seller_est_flip_profit: number;
    seller_est_rental_rehab: number;
    seller_est_cashflow: number;
    seller_avg_rent: number;
    accuracy_score_value: number;
    accuracy_score_rent: number;
    short_list_price: string;
}

export interface DBApiSaleActivityMetric {
    current: number;
    previous: number;
    change: number;
}

export interface DBApiSaleActivity {
    sold_under_market_value: DBApiSaleActivityMetric;
    average_sale_price: DBApiSaleActivityMetric;
    average_ppsfc: DBApiSaleActivityMetric;
    average_dom: DBApiSaleActivityMetric;
    closed_count: DBApiSaleActivityMetric;
    pending_count: DBApiSaleActivityMetric;
    new_listings: DBApiSaleActivityMetric;
    flip_count: DBApiSaleActivityMetric;
}


export interface DBApiStatusActivity {
    Active: {
        count: number;
        percentage: number;
    };
    ActiveUnderContract: {
        count: number;
        percentage: number;
    };
    Canceled: {
        count: number;
        percentage: number;
    };
    Closed: {
        count: number;
        percentage: number;
    };
    ComingSoon: {
        count: number;
        percentage: number;
    };
    Expired: {
        count: number;
        percentage: number;
    };
    Pending: {
        count: number;
        percentage: number;
    };
    TempOffMarket: {
        count: number;
        percentage: number;
    };
    Withdrawn: {
        count: number;
        percentage: number;
    };
}