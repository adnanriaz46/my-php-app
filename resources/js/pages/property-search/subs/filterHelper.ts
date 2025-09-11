import {UseFormReturnType} from '@inertiajs/vue3';
import {SavedSearchFull} from "@/types/propertySearch";

export const locationFields = [
    'proximity_object',
    'comps_sub_prop_id',
    'distance_max',
    'county',
    'city_name_keyword',
    'zip'
]

export const dealTypeFields = [
    'deal_type'
]

export const statusFields = [
    'status'
]

export const goldFilterFields = [
    'gold_filter_type',
    'delta_min',
    'delta_max',
    'est_profit_min',
    'est_profit_max',
    'est_cashflow_min',
    'est_cashflow_max',
    'accuracy_score_rent',
    'accuracy_score_value'
]
export const domFilterFields = [
    'dom_min',
    'dom_max',
    'closed_date_min',
    'closed_date_max',
]
export const moreFilterFields = [
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
    'est_arv_max'
]


export function isLocationValuesExists(form: Record<string, any>) {
    return isValuesExist(form, locationFields)
}

export function isDealTypeValuesExists(form: Record<string, any>) {
    return isValuesExist(form, dealTypeFields)
}

export function isStatusValuesExists(form: Record<string, any>) {
    return isValuesExist(form, statusFields)
}

export function isMoreValuesExists(form: Record<string, any>) {
    return isValuesExist(form, moreFilterFields)
}

export function isDomValuesExists(form: Record<string, any>) {
    return isValuesExist(form, domFilterFields)
}

export function isGoldValuesExists(form: Record<string, any>) {
    return isValuesExist(form, goldFilterFields)
}

export function isValuesExist(form: Record<string, any>, fields: string[]) {
    return fields.some(field => {
        const val = form[field]
        if (Array.isArray(val)) {
            return val.length > 0
        }
        if (typeof val === 'string') {
            return val.trim() !== ''
        }

        if (typeof val == 'number') {
            return val !== 0 && !isNaN(val)
        }

        if (typeof val === 'boolean') {
            return val;
        }
        // For other types (number, boolean), you can decide the logic:
        return val !== null && val !== undefined && val !== ''
    })
}


export function resetFormFields(form: Record<string, any>, fields: string[]) {
    fields.forEach(field => {
        const val = form[field];
        if (Array.isArray(val)) {
            form[field] = [];
        } else if (typeof val === 'string') {
            form[field] = '';
        } else if (typeof val === 'number') {
            form[field] = 0;
        } else if (typeof val === 'boolean') {
            form[field] = false;
        } else {
            form[field] = null;
        }
    });
}

export interface MileSearchAddress {
    address: string;
    lat?: number;
    lng?: number;
}

export function mapSavedSearchToForm(
    form: UseFormReturnType<any>,
    data: SavedSearchFull
): void {
    if (!data) return;

    const fieldMap: Record<string, string> = {
        saved_search_name: 'savedSearchName',
        saved_search_id: 'savedSearchId',
        saved_search_as_new: 'savedSearchAsNew'
    };

    for (const key in data) {
        const formKey = fieldMap[key] || key;
        if (form.hasOwnProperty(formKey)) {
            const value = data[key as keyof SavedSearchFull];
            form[formKey] = value !== null && value !== undefined ? value : '';
        }
    }
}
