import axios from "axios";
import {returnErrorHandleInApp, returnResponseInApp} from "@/lib/utils";
import {GetInAppData} from "@/types";
import {ZillowPListingMain} from "@/lib/zilowAndlocationUtil";
import {BuyerFinancingFormData} from "@/types/property";


export async function getAskAiPropertySettingsMessage(listingType: "Unlisted" | "Listed" | "Other"): Promise<GetInAppData<string>> {
    try {
        const response = await axios.get(route('property.request.ask_ai-setting-message'), {
            params: {
                listingType: listingType
            }
        });
        return returnResponseInApp(response?.data)
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}

export async function getAskAiUnlistedPropertyDetail(address: string): Promise<GetInAppData<ZillowPListingMain>> {
    try {
        const response = await axios.get(route('property.request.ask_ai_unlisted_details'), {
            params: {
                address: address
            }
        });
        return returnResponseInApp(response?.data)
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}


export async function getBuyerFinancingFormData(): Promise<GetInAppData<BuyerFinancingFormData>> {
    try {
        const response = await axios.get(route('get.data.buyer_financing_form_data'));
        return returnResponseInApp(response?.data)
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}
