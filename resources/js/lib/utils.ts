import {clsx, type ClassValue} from 'clsx';
import {twMerge} from 'tailwind-merge';
import {GetInAppData} from "@/types";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function getDateDaysBefore(days: number): string {
    const date = new Date();
    date.setDate(date.getDate() - days);
    return date.toISOString().split('T')[0];
}


export function returnResponseInApp(responseData: any): GetInAppDany<any> {
    if (!responseData) {
        return {error: 'Request invalid'}
    }

    return {data: responseData?.data ?? responseData, error: responseData.error ?? null, success: responseData.success ?? 'success'};
}

export function returnErrorHandleInApp(errorHandle: any): GetInAppData<any> {
    return {error: 'Request error: ' + (errorHandle.response?.data?.message || errorHandle.response?.data?.toString() || errorHandle.message)};
}

export function isMobileUserAgent(userAgent: string): boolean {
    return /Mobile|webOS|BlackBerry|IEMobile|MeeGo|mini|Fennec|Android|iP(ad|od|hone)/i.test(userAgent);
}