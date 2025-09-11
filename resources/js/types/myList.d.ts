import {UnlistedSavedListTypes} from "@/lib/zilowAndlocationUtil";

export
export interface MyPropertyList {
    name: string;
    id: number;
    property_ids: number[];
    remarks: string;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface MyUnlistedList {
    name: string;
    id: number;
    addresses: UnlistedSavedListTypes[];
    remarks: string;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface SuppressProperty {
    id: number,
    property_id: number,
    user_id: number,
    created_at: string;
    updated_at: string;
}
