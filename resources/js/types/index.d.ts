import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import { UserType } from "@/types/userTypes";



export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href?: string;
}

export interface NavItem {
    title: string;
    href?: string;
    icon?: LucideIcon;
    isActive?: boolean;
    tIcon?: string;
    prefix?: string;
    subMenu?: NavItem[] | null,
    restrictedUserType?: UserType[] | null
}

export interface SharedData extends PageProps {
    name: string;
    // quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    success: string,
    recordId: number
    pageQueryParams: string | null;
}

export interface User {
    id: number;
    uuid: string;
    name: string;
    email: string;

    ip_address_text: string;
    uuid: string;
    first_name: string;
    last_name: string;
    street_address: string;
    city: string;
    state: string;
    zip: string;
    phone_number: string;
    profile_picture: string;
    about_me: string;

    company_email: string;
    company_logo: string;
    company_name: string;
    brokerage_name: string;
    agent_license_number: string;
    website: string;
    facebook_url: string;
    instagram_url: string;
    linkedin_url: string;

    user_type: UserType;
    wholesale_enabled: boolean;
    new_user_tour: string[]; // jsonb type
    total_rating_sum: number; // double precision
    searches_count: number;

    affiliate_eligible: boolean;
    affiliate_slug: string;
    affiliated_user: bigint;
    assessment: bigint;
    assessment_filled: boolean;

    email_verified: boolean;
    email_unsubscribed_global: boolean;
    email_unsubscribed_list_preference: string[]; // jsonb type

    company_email_verified_at: string | null;
    company_email_verified: boolean;

    ip_location_lat: string;
    ip_location_lng: string;
    ip_address: string[]; // jsonb type
    last_login: Date;
    visit_counts: number;

    stripe_customer_id: string;
    stripe_subscription_id: string;
    subscribed_counties: string[]; // jsonb type
    subscription_period_monthly: boolean;
    subscription_start: Date;
    subscription_end: Date;
    subscription_status: string;

    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface LeadSource {
    id: number;
    user_id: number;
    page_name: string;
    page_url: string;
    user_agent: string;
    visited_at: Date;
    created_at: Date;
    updated_at: Date;
}

export interface EmailCategory {
    name: string;
    description: string;
}

export interface BuyBox {
    id: bigint;
    user_id: bigint;
    investment_strategy?: []; // JSONB, using `any`, or consider a specific structure
    counties_invest?: []; // JSONB
    property_types?: []; // JSONB
    arv_min: number;
    arv_max: number;
    bath_min: number;
    bath_max: number;
    bed_min: number;
    bed_max: number;
    cashflow_min: number;
    cashflow_max: number;
    delta_psf_min: number;
    delta_psf_max: number;
    flip_profit_min: number;
    flip_profit_max: number;
    price_min: number;
    price_max: number;
    sqft_min: number;
    sqft_max: number;
    year_build_min: number;
    year_build_max: number;
    created_at: Date; // Mapping timestamp to `Date`
    updated_at: Date;
}

export interface GetInAppData<T> {
    data: T,
    error: string // if any errors
    success: string // if any success messages
}

export interface comboboxBoxValues {
    key: string;
    name: string;
}

export interface UpgradeFeature {
    id: number;
    feature: string;
    description: string;
    group: UserType;
    order: number;
    no_access: boolean;
    created_at: string;
    updated_at: string;
}

export interface Testimonial {
    id: number;
    description: string;
    email: string;
    name: string;
    profile_image: string | null;
    published_date: string | null;
    rate: number;
    title: string;
    created_at: string;
    updated_at: string;
}

export interface CountyState {
    id: number;
    county: string;
    display: string;
    fips: string | null;
    state: string;
    slug: string;
    created_at: string;
    updated_at: string;
}

export interface RequestAShowing {
    id: number;
    full_street_address: string;
    city: string;
    state: string;
    zip: string;
    property_id: string;
    preferred_time: string;
    message: string | null;
    wholesale_id: number | null;
    user_id: number;
    user?: User;
    wholesale?: any;
    created_at: string;
    updated_at: string;
}

export interface AskQuestion {
    id: number;
    full_street_address: string | null;
    city: string | null;
    state: string | null;
    zip: string | null;
    name: string | null;
    email: string | null;
    phone: string | null;
    preferred_contact_method: string | null;
    property_id: string;
    question: string | null;
    wholesale_id: string | null;
    user_id: number;
    user?: User;
    wholesale?: any;
    created_at: string;
    updated_at: string;
}

export interface ContactUsRequest {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
    message: string;
    status: 'pending' | 'replied' | 'closed';
    replied_at?: string;
    created_at: string;
    updated_at: string;
    full_name: string;
}

export interface InstantOffer {
    id: number;
    address: string | null;
    full_street_address: string | null;
    city: string | null;
    state: string | null;
    zip: string | null;
    property_id: number;
    name: string | null;
    email: string | null;
    phone: string | null;
    buyer_name_llc: string | null;
    deposit_price: number | null;
    offer_price: number | null;
    preferred_closing_date: string | null;
    note: string | null;
    agent_name: string | null;
    agent_email: string | null;
    agent_commission: number | null;
    tin: string | null;
    assignor_name: string | null;
    wholesale_id: number | null;
    user_id: number;
    user?: User;
    wholesale?: any;
    created_at: string;
    updated_at: string;
}


export type BreadcrumbItemType = BreadcrumbItem;



interface SupportTicket {
    id: number
    user_id: number
    ticket_number: string
    title: string
    description: string
    categories: string[]
    attachments: string[] | null
    status: 'open' | 'in_progress' | 'waiting_for_user' | 'waiting_for_admin' | 'closed'
    priority: 'low' | 'medium' | 'high' | 'urgent'
    closed_at: string | null
    closed_by: number | null
    created_at: string
    updated_at: string
    user?: User
    closedBy?: User
    replies?: SupportTicketReply[]
    categories_string?: string
    priority_color_class?: string
    status_color_class?: string
}

interface SupportTicketReply {
    id: number
    support_ticket_id: number
    user_id: number
    message: string
    attachments: string[] | null
    is_admin_reply: boolean
    created_at: string
    updated_at: string
    user?: User
}

export interface UserReferralW9 {
    id: number;
    name: string;
    file_url: string;
    user_id: number;
    approved: boolean;
    remarks: string;
    created_at: string;
    updated_at: string;
}

export interface UserReferralEarning {
    id: number;
    user_id: number;
    from_user_id: number;
    amount: number;
    description: string;
    status: 'pending' | 'approved' | 'rejected' | 'paid' | 'cancelled';
    type: 'subscription_upgrade' | 'first_payment' | 'recurring_payment' | 'bonus';
    reference_id?: number;
    reference_type?: string;
    paid_at?: string;
    notes?: string;
    created_at: string;
    updated_at: string;
    user?: User;
    fromUser?: User;
}