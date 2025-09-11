import axios from "axios";
import { returnErrorHandleInApp, returnResponseInApp } from "@/lib/utils";
import { GetInAppData } from "@/types";
import { paramsDBApiGetProperty } from "@/types/DBApi";



export async function requestWholesaleAddress(params: { property_id: number }): Promise<GetInAppData<null>> {
    try {
        const response = await axios.post(route('property.request.wholesale_address'), params);
        return returnResponseInApp(response?.data)
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}
export async function getSuppressedPropertyIds(): Promise<GetInAppData<null>> {
    try {
        const response = await axios.get(route('property.search.get-hidden-properties'));
        return returnResponseInApp(response?.data)
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}

export interface PropertyRecentViewParamsType {
    id: number;
    geo_address: string;
    street_address: string;
    city: string;
    state: string;
    zip: string;
    status: string;
    platform?: string | null;
    useragent?: string | null;
    is_mobile?: string | null;
    is_locked?: boolean,
}

export async function updateRecentViewProperty(params: PropertyRecentViewParamsType): Promise<any> {
    try {
        params.is_mobile = /Mobi|Android/i.test(navigator.userAgent);
        params.platform = navigator.platform || 'unknown';
        params.useragent = navigator.userAgent;

        const response = await axios.post(route('property.recent_view'), params);
        return response.data;
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}

export interface UnlistedRecentViewParamsType {
    zpid: number;
    address: string;
    platform?: string | null;
    useragent?: string | null;
    is_mobile?: string | null;
}

export async function updateRecentViewUnlisted(params: UnlistedRecentViewParamsType): Promise<any> {
    try {
        params.is_mobile = /Mobi|Android/i.test(navigator.userAgent);
        params.platform = navigator.platform || 'unknown';
        params.useragent = navigator.userAgent;

        const response = await axios.post(route('property.recent_view_unlisted'), params);
        return response.data;
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}

export interface paramUpdateSearchHistory extends paramsDBApiGetProperty {
    is_mobile: boolean,
    platform: string,
    useragent: string,
}

export async function updateSearchHistory(params: paramUpdateSearchHistory): Promise<any> {
    try {
        params.is_mobile = /Mobi|Android/i.test(navigator.userAgent);
        params.platform = navigator.platform || 'unknown';
        params.useragent = navigator.userAgent;

        const response = await axios.post(route('filter.update_history'), params);
        return response.data;
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}

export function comSepToArray(commaSeparatedString: unknown): string[] {
    if (typeof commaSeparatedString === 'string') {
        return commaSeparatedString.split(',').map((val) => val.trim());
    }
    return [];
}

export const marketCoverageStates = [
    'PA',
    'NJ',
    'DE',
    'MD',
    'VA',
    'WV',
    'DC',
]


export function isInMarketCoverage(state: string, county: string = ''): boolean {

    if (county) {
        // TODO: Add county to market coverage
    }
    if (marketCoverageStates.includes(state)) {
        return true;
    }
    return false;
}