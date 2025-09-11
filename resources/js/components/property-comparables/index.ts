export { default as PropertyComparable } from './PropertyComparables.vue'


export function getDateBeforeDays(days: number){
    const date = new Date();
    date.setDate(date.getDate() - days);

   return date.toISOString().split('T')[0];
}
