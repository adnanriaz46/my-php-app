export { default as UnlistedPropertySheet } from './UnlistedPropertySheet.vue'
export { default as AddToListUnlisted } from './AddToListUnlisted.vue'

export function yesOrNo(bool): string {
    if (bool === true) {
        return 'Yes';
    } else if (bool === false) {
        return 'No';
    }
    return 'N/A';
}

export function getClassByNumber(num: number): string {
    if (num == 0) {
        return 'text-muted-foreground';
    } else if (num < 0) {
        return 'text-red-600';
    }
    return 'text-green-500';
}
