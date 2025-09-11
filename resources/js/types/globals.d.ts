import type {route as routeFn} from 'ziggy-js';

declare global {
    const route: typeof routeFn;

}

interface SelectBoxOption {
    key: string | number
    value: string
}


