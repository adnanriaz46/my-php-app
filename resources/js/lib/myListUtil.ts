import axios from "axios";
import {MyPropertyList, MyUnlistedList} from "@/types/myList";
import {GetInAppData} from "@/types";
import {returnErrorHandleInApp, returnResponseInApp} from "@/lib/utils";

export async function getMyPropertyList(): Promise<GetInAppData<MyPropertyList[]>> {
    try {
        const response = await axios.get(route('property.my_list.get_property'));
        return returnResponseInApp(response?.data);
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}

export async function getMyUnlistedList(): Promise<GetInAppData<MyUnlistedList[]>> {
    try {
        const response = await axios.get(route('property.my_list.get_unlisted'));
        return returnResponseInApp(response?.data);
    } catch (error: any) {
        return returnErrorHandleInApp(error);
    }
}


