import {useNumber, useDateFormat} from "@/composables/useFormat";

const {formatNumber, formatPrice, formatPercent} = useNumber()
const {formatDate, formatDateTime} = useDateFormat()

export type formatColumnValueType = 'text' | 'number' | 'price' | 'bool' | 'percent' | 'date' | 'datetime' | 'img';

export interface ListTableColumn {
    key: string // will be of the DBApiPropertyList
    title: string // title
    shortTitle: string// short title
    columnWidth?: number
    format?: formatColumnValueType
    line2Line?: boolean,
    prefix?: string,
    suffix?: string
}

export const tableColumns: ListTableColumn[] = [
    {key: "id", title: "Revamp ID", shortTitle: "R. ID", columnWidth: 80, format: 'text', line2Line: false},
    {key: "status", title: "Status", shortTitle: "Status", columnWidth: 60, format: 'text', line2Line: false},
    {key: "wholesale", title: "Deal Type", shortTitle: "Deal", columnWidth: 80, format: 'text', line2Line: false},
    {key: "list_price", title: "List Price", shortTitle: "Price", columnWidth: 100, format: 'price', line2Line: false},
    {key: "office_info", title: "Office Info", shortTitle: "Office", columnWidth: 250, format: 'text', line2Line: true},
    {key: "address", title: "Address", shortTitle: "Addr", columnWidth: 300, format: 'text', line2Line: true},
    {key: "bedrooms_count", title: "Bedrooms", shortTitle: "Beds", columnWidth: 45, format: 'number', line2Line: false},
    {key: "bathrooms_total_count", title: "Bathrooms", shortTitle: "Baths", columnWidth: 45, format: 'number', line2Line: false},
    {key: "total_finished_sqft", title: "Total Sqft", shortTitle: "Sqft", columnWidth: 75, format: 'number', line2Line: false},
    {key: "structure_type", title: "Structure Type", shortTitle: "Type", columnWidth: 120, format: 'text', line2Line: false},
    {key: "medianrent", title: "Median Rent", shortTitle: "Rent", columnWidth: 80, format: 'price', line2Line: false},
    {key: "est_avm", title: "Est. AVM", shortTitle: "AVM", columnWidth: 100, format: 'price', line2Line: false},
    {key: "est_arv", title: "Est. ARV", shortTitle: "ARV", columnWidth: 100, format: 'price', line2Line: false},
    {key: "est_cashflow", title: "Est. Cashflow", shortTitle: "Cashflow", columnWidth: 80, format: 'price', line2Line: false},
    {key: "delta_psf", title: "Delta PSF", shortTitle: "Δ PSF", columnWidth: 80, format: 'price', line2Line: false},
    {key: "est_profit", title: "Est. Profit", shortTitle: "Profit", columnWidth: 80, format: 'price', line2Line: false},
    {key: "latitude", title: "Latitude", shortTitle: "Lat", columnWidth: 100, format: 'text', line2Line: false},
    {key: "longitude", title: "Longitude", shortTitle: "Long", columnWidth: 100, format: 'text', line2Line: false},
    {key: "image", title: "Image", shortTitle: "Img", columnWidth: 60, format: 'img', line2Line: false},
    {key: "real_dom", title: "Real DOM", shortTitle: "R/DOM", columnWidth: 65, format: 'number', line2Line: false},
    {key: "close_date", title: "Close Date", shortTitle: "D/Close", columnWidth: 120, format: 'date', line2Line: false},
    {key: "buyer_financing", title: "Buyer Financing", shortTitle: "Finance", columnWidth: 100, format: 'text', line2Line: true},
    {key: "dom", title: "MLS DOM", shortTitle: "M/DOM", columnWidth: 65, format: 'number', line2Line: false},
    {key: "mls_number", title: "MLS Number", shortTitle: "MLS #", columnWidth: 130, format: 'text', line2Line: false},
    {key: "low_comps_ppsf", title: "Low Comps PPSF", shortTitle: "L/PPSF", columnWidth: 80, format: 'price', line2Line: false},
    {key: "high_comps_ppsf", title: "High Comps PPSF", shortTitle: "H/PPSF", columnWidth: 80, format: 'price', line2Line: false},
    {key: "listing_entry_date", title: "Listing Date", shortTitle: "List Dt", columnWidth: 100, format: 'date', line2Line: false},
    {key: "price_per_sqft", title: "Price/Sqft", shortTitle: "$/Sqft", columnWidth: 80, format: 'price', line2Line: false},
    {key: "close_price", title: "Close Price", shortTitle: "Close $", columnWidth: 100, format: 'price', line2Line: false},
    {key: "price_per_sqft_closed", title: "PPSF Closed", shortTitle: "PPSF/Cl", columnWidth: 80, format: 'price', line2Line: false},
    {key: "list_agent_email", title: "Agent Email", shortTitle: "A/Email", columnWidth: 250, format: 'text', line2Line: false},
    {key: "seller_avm", title: "Seller AVM", shortTitle: "S. AVM", columnWidth: 100, format: 'price', line2Line: false},
    {key: "seller_arv", title: "Seller ARV", shortTitle: "S. ARV", columnWidth: 100, format: 'price', line2Line: false},
    {key: "seller_est_flip_rehab", title: "Seller Flip Rehab", shortTitle: "S.Flip R", columnWidth: 100, format: 'price', line2Line: false},
    {key: "seller_est_flip_profit", title: "Seller Flip Profit", shortTitle: "S.Flip $", columnWidth: 100, format: 'price', line2Line: false},
    {key: "seller_est_rental_rehab", title: "Seller Rental Rehab", shortTitle: "S.Rent R", columnWidth: 100, format: 'price', line2Line: false},
    {key: "seller_est_cashflow", title: "Seller Cashflow", shortTitle: "S. C/F", columnWidth: 100, format: 'price', line2Line: false},
    {key: "seller_avg_rent", title: "Seller Avg Rent", shortTitle: "S. Avg/Rent", columnWidth: 100, format: 'price', line2Line: false},
    {key: "accuracy_score_value", title: "Accuracy Value", shortTitle: "Acc. Val", columnWidth: 100, format: 'text', line2Line: false},
    {key: "accuracy_score_rent", title: "Accuracy Rent", shortTitle: "Acc. Rent", columnWidth: 100, format: 'text', line2Line: false},
    {key: "short_list_price", title: "Short List Price", shortTitle: "Short $", columnWidth: 70, format: 'text', line2Line: false},
    {key: "county_state", title: "County/State", shortTitle: "County", columnWidth: 140, format: 'text', line2Line: false},
    // {key: "mile_range_from_subject", title: "Miles from Subject", shortTitle: "Miles", columnWidth: 100, format: 'text', line2Line: false},
];

const columnMap = Object.fromEntries(
    tableColumns.map(col => [col.key, col])
);

export function formatValue(value: string, type: formatColumnValueType) {
    if (!value) return 'N/A';
    switch (type) {
        case "datetime":
            return formatDateTime(value ?? '')
        case "bool":
            return value ? "Yes" : 'No'
        case "date":
            if (!value || value == '1970-01-01' || value == '0000-00-00') return 'N/A'
            return formatDate(value ?? '')
        case "number":
            return formatNumber(value)
        case"percent":
            return formatPercent(value)
        case "price":
            return formatPrice(value)
        case "text":
        default:
            return value
    }
}

export function getColumns(...keys: string[]): ListTableColumn[] {
    const seen = new Set<string>();
    const columns: ListTableColumn[] = [];

    keys.forEach((key) => {
        if (seen.has(key)) return; // skip duplicates
        seen.add(key);
        const col = columnMap[key];
        if (col) {
            columns.push(col);
        } else {
            console.warn(`⚠️ Unknown column key: "${key}"`);
        }
    });

    return columns;
}

export function isColumnExists(key: string, selectedColumns: ListTableColumn[]): boolean {
    return selectedColumns.some((item) => item.key === key);
}

export function addColumn(key: string, selectedColumns: ListTableColumn[]): ListTableColumn[] {
    const newColumn = tableColumns.find((item) => item.key === key);
    if (!newColumn) return selectedColumns;
    if (selectedColumns.some((col) => col.key === key)) return selectedColumns;
    return [...selectedColumns, newColumn];
}

export function removeColumn(key: string, selectedColumns: ListTableColumn[]): ListTableColumn[] {
    return selectedColumns.filter((item: ListTableColumn) => item.key !== key);
}

