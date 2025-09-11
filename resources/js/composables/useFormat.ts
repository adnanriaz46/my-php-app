export function useNumber() {
    const formatNumber = (value: number | string, decimal: number = 0, locales = 'en-US') => {
        if (value === null || value === undefined || value === '') return null;
        return new Intl.NumberFormat(locales, {
            minimumFractionDigits: decimal,
            maximumFractionDigits: decimal,
        }).format(Number(value));
    };
    const formatPrice = (
        value: number | string,
        decimal: number = 0,
        currency = 'USD',
        locales = 'en-US'
    ) => {
        return new Intl.NumberFormat(locales, {
            style: 'currency',
            currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: decimal,
          }).format(Number(value));
    }

    const formatPercent = (value: number | string, maxDigit = 0, locales = 'en-US') => {
        return new Intl.NumberFormat(locales, {
            style: 'percent',
            minimumFractionDigits: maxDigit,
        }).format(Number(value))
    }

    const formatPhone = (value: number | string) => {
        if (!value) return '';
        const cleaned = ('' + value).replace(/\D/g, '');
        const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
        if (match) {
            return `(${match[1]}) ${match[2]}-${match[3]}`;
        }

        return value;
    }

    return {
        formatNumber,
        formatPrice,
        formatPercent,
        formatPhone
    }
}

export function useDateFormat() {
    const formatDate = (
        value: string | number | Date,
        locales = 'en-US',
        options: Intl.DateTimeFormatOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }
    ) => {
        const date = new Date(value)
        return isNaN(date.getTime()) ? '' : new Intl.DateTimeFormat(locales, options).format(date)
    }
    const formatDateTime = (
        value: string | number | Date,
        locales = 'en-US',
        options: Intl.DateTimeFormatOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        }
    ) => {
        const date = new Date(value);
        return isNaN(date.getTime()) ? '' : new Intl.DateTimeFormat(locales, options).format(date);
    };

    const formatGetDays = (value: string | number | Date) => {

        const inputDate = new Date(value);
        const nowUTC = new Date();
        const estNow = new Date(
            nowUTC.toLocaleString('en-US', {timeZone: 'America/New_York'})
        );
        const start = new Date(inputDate.toDateString());
        const end = new Date(estNow.toDateString());

        const diffTime = end.getTime() - start.getTime();
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        return diffDays;
    }


    const getDateBeforeDays = (days: number) => {
        const date = new Date();
        date.setDate(date.getDate() - days);
    
       return date.toISOString().split('T')[0];
    }

    return {
        formatDate, formatDateTime, formatGetDays, getDateBeforeDays
    }
}

export function useTextFormat() {

    const formatToCapitalizeEachWord = (text: string | null | undefined): string =>
        text
            ? text
                .toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ')
            : '';
    const formatToCapitalize = (text: string | null | undefined) =>
        text ? text.charAt(0).toUpperCase() + text.slice(1).toLowerCase() : ''

    const formatToUpperCase = (text: string | null | undefined): string =>
        text ? text.toUpperCase() : '';

    const formatToLowerCase = (text: string | null | undefined): string =>
        text ? text.toLowerCase() : '';
    const formatToTruncate = (text: string | null, length = 50, suffix = '...') => {
        if (!text) return ''
        return text.length > length ? text.slice(0, length) + suffix : text
    }

    const formatUserAgent = (ua: string): string => {
        const browserMatch = ua.match(/(Chrome|Firefox|Safari|Edg|Opera)\/([\d.]+)/);
        const osMatch = ua.match(/\(([^)]+)\)/);

        const browser = browserMatch ? `${browserMatch[1]} ${browserMatch[2].split('.')[0]}` : 'Unknown Browser';
        const osRaw = osMatch ? osMatch[1] : 'Unknown OS';

        let os = 'Unknown OS';
        if (osRaw.includes('Windows NT 10.0')) os = 'Windows 10';
        else if (osRaw.includes('Mac OS X')) os = 'macOS';
        else if (osRaw.includes('Android')) os = 'Android';
        else if (osRaw.includes('iPhone')) os = 'iOS';

        return `${browser} on ${os}`;
    };

    return {
        formatUserAgent,
        formatToCapitalize,
        formatToLowerCase,
        formatToTruncate,
        formatToUpperCase,
        formatToCapitalizeEachWord
    }
}

