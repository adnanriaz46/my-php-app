import axios from "axios";
import {
    DBApi,
    DBApiAverageCompsProperty,
    DBApiBuyer,
    DBApiCalValData,
    DBApiMlsHistory, DBApiPropertyCount,
    DBApiPropertyFull,
    DBApiPropertyList, DBApiPropertyMinimal, paramsDBApiGetProperty, DBApiOwnershipData,
    DBApiSoldUnderMarketValue,
    DBApiDatatreePropertyInfo,
    DBApiSaleActivity,
    DBApiStatusActivity
} from "@/types/DBApi";


export async function getDBApiCalculatedValueData(zip: string, propertyType: string, lat: string, lng: string, sqft: number, listPrice: number, address: string): Promise<DBApi<DBApiCalValData>> {
    try {

        const response = await axios.get(route('data-db-api.get_calculated_values'), {
            params: {
                zip_code: zip,
                property_type: propertyType,
                latitude: lat,
                longitude: lng,
                sqft: sqft,
                list_price: listPrice,
                address: address
            }
        });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export async function getDBApiMLSHistory(address: string): Promise<DBApi<DBApiMlsHistory>> {
    try {
        const response = await axios.get(route('data-db-api.get_mls_history'), {
            params: {
                address: address
            }
        });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export interface GetAvgCompsParams {
    sqft_min?: number;
    sqft_max?: number;
    lat?: number;
    lng?: number;
    property_type?: string[];
    closed_on?: string;
    zip?: string;
    distance?: number;
    ids?: string[];
}

export async function getAvgComps(params: GetAvgCompsParams): Promise<DBApi<DBApiAverageCompsProperty>> {
    try {
        const response = await axios.get(route('data-db-api.get_avg_comps'), {
            params: params
        });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}
export async function getPropertyMinimalListByText(query: string, excludeNegativeStatus: boolean = false): Promise<DBApi<DBApiPropertyMinimal[]>> {
    try {
        const response = await axios.get(route('data-db-api.get_property_minimal_list_by_text'), {
            params: {
                query: query,
                exclude_negative_status: excludeNegativeStatus
            }
        });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export async function getAskAiProperty(params: { id: number }): Promise<DBApi<string>> {
    try {
        const response = await axios.get(route('data-db-api.get_ask_ai'), {
            params: params
        });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export async function getPropertyList(params: paramsDBApiGetProperty): Promise<DBApi<DBApiPropertyList[]>> {
    try {
        const response = await axios.post(route('data-db-api.get_property_list'), params);
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export async function getPropertyCount(params: paramsDBApiGetProperty): Promise<DBApi<DBApiPropertyCount>> {
    try {
        const response = await axios.post(route('data-db-api.get_property_count'), params);
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export async function getProperty(params: paramsDBApiGetProperty): Promise<DBApi<DBApiPropertyFull[]>> {
    try {
        const response = await axios.post(route('data-db-api.get_property'), params);
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}


export interface SearchBuyersParams {
    county?: string;
    state?: string;
    address?: string;
    zip?: string;
    lat?: number;
    lng?: number;
    range?: number;
    fips?: string;
    days?: number;
    list_name?: string;
}

export async function searchBuyers(params: SearchBuyersParams): Promise<DBApi<DBApiBuyer[]>> {
    try {
        const response = await axios.get(route('data-db-api.search_buyers'), { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}




export interface GetSoldUnderMarketValueParams {
    range: number;
    state?: string;
    county?: string;
    city?: string;
    zip?: string;
    limit?: number;
}
export async function getSoldUnderMarketValue(params: GetSoldUnderMarketValueParams): Promise<DBApi<DBApiSoldUnderMarketValue[]>> {

    try {
        const response = await axios.get(route('data-db-api.get_sold_under_market_value'), { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export interface GetSalesActivityParams {
    range: number;
    state?: string;
    county?: string;
    city?: string;
    zip?: string;
}

export async function getSalesActivity(params: GetSalesActivityParams): Promise<DBApi<DBApiSaleActivity>> {
    try {
        const response = await axios.get(route('data-db-api.get_sales_activity'), { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export interface GetStatusActivityParams {
    state?: string;
    county?: string;
    city?: string;
    zip?: string;
}

export async function getStatusActivity(params: GetStatusActivityParams): Promise<DBApi<DBApiStatusActivity>> {
    try {
        const response = await axios.get(route('data-db-api.get_status_activity'), { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}





function returnResponse(responseData: any): DBApi<any> {
    if (!responseData) {
        return { error: 'Request invalid' }
    } else {
        if (!responseData?.status) {
            return { error: responseData?.message ?? 'Request has no response' }
        }
    }

    return responseData;
}

function returnErrorHandle(errorHandle: any): DBApi<any> {
    return { error: 'Request error: ' + (errorHandle.response?.data?.message || errorHandle.response?.data?.toString() || errorHandle.message) };
}

export interface GetOwnershipDataParams {
    full_street_address: string;
    city: string;
    state: string;
    zip: string;
    county: string;
}

export async function getOwnershipData(params: GetOwnershipDataParams): Promise<DBApi<DBApiOwnershipData[]>> {
    try {
        const response = await axios.get(route('data-db-api.get_ownership_data'), { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

export interface GetDatatreePropertyInfoParams {
    full_street_address: string;
    city: string;
    state: string;
    zip: string;
    county: string;
}

export async function getDatatreePropertyInfo(params: GetDatatreePropertyInfoParams): Promise<DBApi<DBApiDatatreePropertyInfo[]>> {
    try {
        const response = await axios.get(route('data-db-api.get_datatree_property_info'), { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}
// Basic search (calls DBApiHelper)

// Keep this one (line 230)
export async function searchBuyersWithSkipTrace(params: SearchBuyersParams): Promise<DBApi<DBApiBuyer[]>> {
    try {
        const response = await axios.get('/buyers/api/search-with-skip-trace', { params });
        return returnResponse(response?.data)
    } catch (error: any) {
        return returnErrorHandle(error);
    }
}

