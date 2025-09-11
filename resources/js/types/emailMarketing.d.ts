import { DBApiPropertyFull } from "./DBApi";
import { User } from "./user";

export interface Contact {
    id: number;
    name: string;
    email: string;
    phone: string;
    price_range: string[];
    tags: string[];
    counties: string[];
    zip: string[];
    deal_type: string[];
    user_id: number;
    email_type: string;
    first_name: string;
    last_name: string;
    created_at: string;
    updated_at: string;
    user: User;
    has_contact_info?: boolean;
    skip_traced_tag_class?: string;
    filtered_tags?: string[];
}

export interface CampaignAdditionalData {
    total_recipients: number;
    sent: number;
    failed: number;
    pending: number;
    unsubscribed: number;
    errors: { message: string; recipients_count: number }[];
    open_rate: number;
    click_rate: number;
}

export interface Campaign {
    id: number;
    user_id: number;
    name: string;
    subject: string;
    email_header: string;
    email_description: string;
    property_id: number;
    property_data: DBApiPropertyFull;
    status: 'draft' | 'scheduled' | 'sent' | 'failed';
    scheduled_at: string | null;
    sent_at: string | null;
    created_at: string;
    updated_at: string;
    recipients_count: number;
    sent_count: number;
    failed_count: number;
    // open_rate: number;
    // click_rate: number;
    additional_data?: CampaignAdditionalData | null;
}

export interface CampaignAllStates {
    processed: number;
    delivered: number;
    bounce: number;
    open: number;
    click: number;
    spam_report: number;
    unsubscribe: number;
    dropped: number;
}

export interface EmailContent {
    subject: string;
    previewText: string;
    body: string;
    ctaText: string;
    ctaUrl: string;
}


export interface CampaignRecipient {
    id: number;
    campaign_id: number;
    contact_id: number;
    email: string;
    name: string;
    status: 'pending' | 'sent' | 'failed' | 'opened' | 'clicked';
    sent_at: string | null;
    opened_at: string | null;
    clicked_at: string | null;
    contact: Contact;
}

export interface CampaignProperty {
    id: number;
    campaign_id: number;
    property_id: string;
    property_data: any;
}