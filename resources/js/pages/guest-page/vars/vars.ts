export const pageVars = {
    logoWhite: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/687fcb6318f11.svg',
    logoBlack: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/687fcbce05901.png',
    logoBlackLocal: '/site-assets/687fcbce05901.png',
    bgVideo: '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743953990873x993873962464881200/home%20page%20background%20video.mp4',
    bgVideoPoster: '//revamp365-storage.s3.amazonaws.com/assets/assets/687f9c5363764.png',
    bgFooter: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/687fcda26b37a.png',
}


export interface GuestFeature {
    title: string;
    description: string;
    image: string | null;
    iframe: string | null; // iframe HTML
}

export interface GuestNavItem {
    title: string;
    href?: string;
    items?: {
        title: string;
        description?: string;
        href?: string;
        icon?: any;
        action?: () => void | null;
    }[];
}