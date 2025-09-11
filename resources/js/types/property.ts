import { User } from "@/types/index";

export const PropertyStatus = Object.freeze({
    Active: 'Active',
    ActiveUnderContract: 'Active Under Contract',
    Canceled: 'Canceled',
    Closed: 'Closed',
    ComingSoon: 'Coming Soon',
    Pending: 'Pending',
    TempOffMarket: 'Temp Off Market',
    Withdrawn: 'Withdrawn',
    Expired: 'Expired',
});

export type PropertyStatus = typeof PropertyStatus[keyof typeof PropertyStatus];

export const PropertyViewTypes = Object.freeze({
    Map: 'Map',
    List: 'List',
    Grid: "Grid",
}); 

export type PropertyViewType = typeof PropertyViewTypes[keyof typeof PropertyViewTypes];

export const PropertySortBy = Object.freeze({
    priceLowHigh: 'List Price (Low to high)',
    priceHighLow: 'List Price (High to low)',
    avmHighLow: 'AVM (High to low)',
    rentHighLow: 'Rent (High to low)',
    cashFlowHighLow: 'Cash Flow (High to low)',
    flipProfitHighLow: 'Flip Profit (High to low)',
    deltaHighLow: 'Delta PSF (High to low)',
    domLowHigh: 'DOM (Shortest to longest)',
    domHighLow: 'DOM (Longest to shortest)',
});

export type PropertySortByType = typeof PropertySortBy[keyof typeof PropertySortBy];

export const getMainImage = (rawImagesString: string | null = null): string => {
    const emptyImage: string = '//s3.amazonaws.com/appforest_uf/f1650658313166x774273891331084700/coming%20soon4.JPG';
    const imageUrl = rawImagesString?.split(',')?.[0].trim() ?? emptyImage;
    if (imageUrl == '') {
        return emptyImage;
    }
    return imageUrl;
}

export interface PropertyViewHistory {
    id: number;
    property_id: number;
    geo_address?: string | null;
    street_address?: string | null;
    city?: string | null;
    state?: string | null;
    zip?: string | null;
    status?: string | null;
    platform?: string | null;
    useragent?: string | null;
    is_mobile?: boolean | null;
    ip?: string | null;
    ip_city?: string | null;
    is_locked: boolean;
    ip_region?: string | null;
    ip_country?: string | null;
    user_id?: number | null;
    user: User;
    created_at: string;  // or `Date` if parsed
    updated_at: string;  // or `Date` if parsed
}

export interface UnlistedViewHistory {
    id: number;
    zpid?: number | null;
    address?: string | null;
    platform?: string | null;
    useragent?: string | null;
    is_mobile?: boolean | null;
    ip?: string | null;
    ip_city?: string | null;
    ip_region?: string | null;
    ip_country?: string | null;
    user_id?: number | null;
    created_at: string; // or Date
    updated_at: string; // or Date
}


export interface AddressRequest {
    id: number;
    is_agreed: 1 | 2 | 0;
    property_id: number;
    remark: string | null;
    wholesale_id: number | null;
    user_id: number;
    user: User;
    created_at: string; // or Date
    updated_at: string; // or Date
}

export interface BuyerFinancingFormData {
    step1: string[],
    step2: string[],
    step3: string[],
    step4: string[]
}

export interface PropertyFilterHistory {
    id: number;
    delta_min?: number | null;
    delta_max?: number | null;
    est_profit_min?: number | null;
    est_profit_max?: number | null;
    est_cashflow_min?: number | null;
    est_cashflow_max?: number | null;
    remarks_public_keywords?: string | null;
    city_name_keyword?: string | null;
    dom_min?: number | null;
    dom_max?: number | null;
    est_arv_min?: number | null;
    est_arv_max?: number | null;
    lot_sqf_min?: number | null;
    lot_sqf_max?: number | null;
    total_finished_sqft_min?: number | null;
    total_finished_sqft_max?: number | null;
    list_price_min?: number | null;
    list_price_max?: number | null;
    medianrent_min?: number | null;
    medianrent_max?: number | null;
    state_or_province_keyword?: string[] | null;
    bedrooms_min?: number | null;
    bedrooms_max?: number | null;
    county?: string[] | null;
    status?: string[] | null;
    list_agent_keyword?: string | null;
    fulladdress_keyword?: string | null;
    structure_type?: string[] | null;
    mls_number?: string | null;
    listing_entry_date_min?: string | null; // Date string
    listing_entry_date_max?: string | null; // Date string
    closed_date_min?: string | null; // Date string
    closed_date_max?: string | null; // Date string
    order_by?: string | null;
    _limit?: number | null;
    zip?: string | null;
    comps_sub_prop_id?: string | null;
    distance_max?: number | null;
    user_lat?: number | null;
    user_lng?: number | null;
    test?: boolean | null;
    deal_type?: string | null;
    year_built_min?: number | null;
    year_built_max?: number | null;
    city_names_avoid?: string[] | null;
    filter_ids?: string[] | null;
    fulladdress_avoid?: string | null;
    all_wholesale?: boolean | null;
    accuracy_score_value?: number | null;
    accuracy_score_rent?: number | null;
    school_district_name?: string[] | null;
    is_mobile?: boolean | null;
    platform?: string | null;
    useragent?: string | null;
    ip?: string | null;
    ip_city?: string | null;
    ip_region?: string | null;
    ip_country?: string | null;
    user_id?: number | null;
    user?: User | null;
    created_at: string; // Date string
    updated_at: string; // Date string
}

export const getPropertyTypeByZillow = (zillowPropertyType: string): string[] => {
    const propertyTypeMap = {
        SINGLE_FAMILY: ['Detached'],
        TOWNHOUSE: ['End of Row/Townhouse', 'Interior Row/Townhouse'],
        MULTI_FAMILY: ['NONE'],
        CONDO: ['Unit/Flat/Apartment', 'Penthouse Unit/Flat/Apartment'],
        LOT: ['Garage/Parking Space', 'Other'],
        APARTMENT: ['Penthouse Unit/Flat/Apartment', 'Unit/Flat/Apartment'],
        UNKNOWN: ['Detached']
    }
    return propertyTypeMap[zillowPropertyType as keyof typeof propertyTypeMap] || ['Detached'];
}

