export interface WholesaleProperty {
    id: number;
    lat: number | null;
    lng: number | null;

    baths: number | null;
    beds: number | null;
    structure_type: string | null;
    total_finished_sqft: string | null;
    lot_sqft: string | null;
    year_built: number | null;
    building_units_total: number | null;
    cool_type: string | null;
    heat_type: string | null;

    tax_annual_amount: string | null;
    tax_assessed_value: string | null;
    tax_id_number: string | null;
    zoning: string | null;

    full_street_address: string | null;
    city_name: string | null;
    state_or_province: string | null;
    zip_code: string | null;
    geo_address: string | null;
    county: string | null;

    status: string | null;
    list_price: number | null;
    closed_date: string | null; // ISO date string
    closed_price: number | null;

    images: string[] | null; // assuming JSON contains array of image URLs or filenames
    hoa: boolean | null;
    listing_agent: string | null;
    listing_office: string | null;
    listing_agent_email: string | null;
    school_district: string | null;
    municipality: string | null;
    remarks_public: string | null;

    rental_estimate: number | null;
    seller_arv: number | null;
    seller_avg_rent: number | null;
    seller_avm: number | null;
    seller_est_cashflow: number | null;
    seller_est_flip_profit: number | null;
    seller_est_flip_rehab: number | null;
    seller_est_rental_rehab: number | null;

    database_id: number | null;

    show_full_address: boolean | null;
    manual_address: boolean | null;
    approved: boolean | null;
    slug: string | null;
    user_id: number | null;

    view_histories_count: number | null;
    pending_address_requests_count: number | null;

    created_at: string;
    updated_at: string;
}
