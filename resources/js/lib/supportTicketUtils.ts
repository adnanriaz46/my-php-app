export const supportTicketStatusOptions = [
    { value: '', label: 'All Statuses' },
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'waiting_for_user', label: 'Waiting for User' },
    { value: 'waiting_for_admin', label: 'Waiting for Admin' },
    { value: 'closed', label: 'Closed' },
];

export const supportTicketStatusOptionsWithoutAll = [
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'waiting_for_user', label: 'Waiting for User' },
    { value: 'waiting_for_admin', label: 'Waiting for Admin' },
    { value: 'closed', label: 'Closed' },
];

export const supportTicketPriorityOptions = [
    { value: '', label: 'All Priorities' },
    { value: 'urgent', label: 'Urgent' },
    { value: 'high', label: 'High' },
    { value: 'medium', label: 'Medium' },
    { value: 'low', label: 'Low' },
];

export const supportTicketPriorityOptionsWithoutAll = [
    { value: 'urgent', label: 'Urgent' },
    { value: 'high', label: 'High' },
    { value: 'medium', label: 'Medium' },
    { value: 'low', label: 'Low' },
];

export const supportTicketSortOptions = [
    { value: 'created_at', label: 'Created Date' },
    { value: 'ticket_number', label: 'Ticket Number' },
    { value: 'title', label: 'Title' },
    { value: 'priority', label: 'Priority' },
    { value: 'status', label: 'Status' },
];

export function getStatusColor(status: string): string {
    switch (status) {
        case 'open':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'waiting_for_user':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
        case 'waiting_for_admin':
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
        case 'closed':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
}

export function getPriorityColor(priority: string): string {
    switch (priority) {
        case 'urgent':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        case 'high':
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
        case 'medium':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'low':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
}

export function getStatusLabel(status: string): string {
    switch (status) {
        case 'open':
            return 'Open';
        case 'in_progress':
            return 'In Progress';
        case 'waiting_for_user':
            return 'Waiting for User';
        case 'waiting_for_admin':
            return 'Waiting for Admin';
        case 'closed':
            return 'Closed';
        default:
            return status;
    }
}

export function getPriorityLabel(priority: string): string {
    switch (priority) {
        case 'urgent':
            return 'Urgent';
        case 'high':
            return 'High';
        case 'medium':
            return 'Medium';
        case 'low':
            return 'Low';
        default:
            return priority;
    }
} 