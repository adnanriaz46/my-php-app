import axios from "axios";
import {router} from "@inertiajs/vue3";
import {ListingOfficesEntity} from "@/types/mbOwnershipData";

export interface PlacePredictionSuggestion {
    description: string;
    place_id: string;
    structured_formatting: {
        main_text: string;
        secondary_text: string;
    };
}

export interface ZillowPlaceLatLng {
    zipCode: string;
    state: string;
    zpid: number;
    lng: number;
    streetName: string;
    city: string;
    maloneId: number;
    streetNumber: string;
    lat: number;
    addressType: string;
    country: string;
}

const GKey = import.meta.env.VITE_GOOGLE_GEO_API;

export async function getPlaceAutocomplete(query: string) {
    try {
        const response = await axios.get(route('get.data.location_place_data'), {params: {search: query,}});
        return response.data;
    } catch (error) {
        console.error("Google Places API Error:", error.response?.data || error.message);
        return null;
    }
}

export async function getGeoAddressInfo(address: string): Promise<GoogleGeocodeResult[] | null> {
    try {
        const response = await axios.get<GoogleGeocodeResponse>(
            'https://maps.googleapis.com/maps/api/geocode/json',
            {
                params: {
                    address,
                    key: GKey,
                },
            }
        );

        const data = response.data;

        // Basic validation
        if (data.status !== 'OK' || !data.results || !Array.isArray(data.results)) {
            console.warn('Invalid Google Geocode response structure:', data);
            return null;
        }

        return data.results;
    } catch (error: any) {
        console.error('Google Geocode API error:', error.response?.data || error.message);
        return null;
    }
}

export function getGeoDataToAddressObject(result: GoogleGeocodeResult): GeoAddressObject {
    let streetNumber = '';
    let route = '';
    let city = '';
    let state = '';
    let county = '';
    let zip = '';
    const geoAddress = result?.formatted_address ?? '';

    for (const component of result.address_components) {
        const types = component.types;

        if (types.includes('street_number')) {
            streetNumber = component.long_name;
        }

        if (types.includes('route')) {
            route = component.long_name;
        }

        if (types.includes('locality')) {
            city = component.long_name;
        }

        if (types.includes('administrative_area_level_1')) {
            state = component.short_name;
        }

        if (types.includes('administrative_area_level_2')) {
            county = component.long_name.replace(/County$/, '').trim();
        }

        if (types.includes('postal_code')) {
            zip = component.long_name;
        }
    }

    return {
        street: `${streetNumber} ${route}`.trim(),
        city,
        state,
        county,
        zip,
        lat: result.geometry.location.lat,
        lng: result.geometry.location.lng,
        geoAddress
    };
}

export async function getZillowPlaceLocationData(query: string) {
    try {
        const response = await axios.get(route('get.data.zillow_place_data'), {params: {search: query}});
        return response.data;
    } catch (error) {
        console.error("Zillow Places API Error:", error.response?.data || error.message);
        return null;
    }
}

export async function getZillowPropertyData(query: string) {
    try {
        const response = await axios.get(route('get.data.zillow_property_data'), {params: {address: query}});
        return response.data;
    } catch (error) {
        console.error("Zillow Property API Error:", error.response?.data || error.message);
        return null;
    }
}

export async function getOwnershipData(streetAddress: string, city: string, state: string, zip: string) {
    try {
        const response = await axios.get(route('get.data.ownership_data'), {
            params: {
                address: streetAddress,
                city: city,
                state: state,
                zip: zip
            }
        });
        return response.data;
    } catch (error) {
        console.error("Zillow Property API Error:", error.response?.data || error.message);
        return null;
    }
}

export function addUnlistedIdToUrl(address: string) {
    const encodedAddress = encodeURIComponent(address)
    const currentUrl = new URL(window.location.href)

    currentUrl.searchParams.set('unlistedid', encodedAddress)

    router.replace({
        url: currentUrl.pathname + currentUrl.search,
        preserveState: true,
        preserveScroll: true,
    })
}

export function addPropertyIdToUrl(id: string | number) {
    const encodedAddress = encodeURIComponent(id)
    const currentUrl = new URL(window.location.href)

    currentUrl.searchParams.set('propertyid', encodedAddress)

    router.replace({
        url: currentUrl.pathname + currentUrl.search,
        preserveState: true,
        preserveScroll: true,
    })
}


export function removeParamsFromUrl() {
    const currentUrl = new URL(window.location.href)
    router.replace({
        url: currentUrl.pathname,
        preserveState: true,
        preserveScroll: true,
    })
}

export interface UnlistedSavedListTypes {
    zpid: number,
    list_price: number,
    address: string,
    beds: number,
    baths: number,
    sqft: number,
    image: number,
    lat: number,
    lng: number,
    zEstimateRent: number,
    zEstimate: number,
    propertyType: number,
}

export interface ZillowPListingMain {
    error?: string | null;
    longitude?: number | null;
    latitude?: number | null;
    imgSrc?: string | null;
    streetAddress?: string | null;
    county?: string | null;
    taxHistory?: ZillowPTaxRecord[] | null;
    annualHomeownersInsurance?: number | null;
    state?: string | null;
    listed_by?: ZillowPListedBy | null;
    yearBuilt?: number | null;
    brokerageName?: string | null;
    isListedByOwner?: boolean | null;
    climate?: ZillowPClimateData | null;
    priceHistory?: ZillowPPriceRecord[] | null;
    rentZestimate?: number | null;
    city?: string | null;
    resoFacts: ZillowPResoFacts | null
    providerListingID?: string | null;
    currency?: string | null;
    listingProvider?: ZillowPListingProvider | null;
    propertyTaxRate?: number | null;
    mortgageRates?: ZillowPMortgageRates | null;
    address?: ZillowPAddress | null;
    cityId?: number | null;
    timeOnZillow?: string | null;
    url?: string | null;
    price?: string | number | null;
    zestimate?: number | null;
    zpid?: number | null;
    countyId?: number | null;
    brokerId?: number | null;
    livingAreaUnits?: string | null;
    comingSoonOnMarketDate?: string | null;
    bathrooms?: number | null;
    bedrooms?: number | null;
    propertyTypeDimension?: string | null;
    mortgageZHLRates?: ZillowPMortgageZHLRates | null;
    pageViewCount?: number | null;
    zestimateHighPercent?: string | null;
    mlsid?: string | null;
    description?: string | null;
    livingArea?: number | null;
    buildingId?: string | null;
    country?: string | null;
    homeType?: string | null;
    datePosted?: string | null;
    zpid?: string | number | null;
    building?: string | null;
    stateId?: number | string | null;
    zipcode?: string | null;
    zestimateLowPercent?: string | null;
    attributionInfo: ZillowPAttributionInfo | null;
    homeStatus?: string | null;
}

export interface ZillowPAttributionInfo {
    buyerAgentName?: null;
    mlsName?: null;
    coAgentLicenseNumber?: null;
    listingOffices?: (ListingOfficesEntity)[] | null;
    lastUpdated?: null;
    buyerAgentMemberStateLicense?: null;
    brokerName?: null;
    listingAgreement?: null;
    infoString10?: null;
    trueStatus?: null;
    infoString3?: null;
    agentEmail?: null;
    agentName?: null;
    attributionTitle?: null;
    mlsId?: null;
    coAgentName?: null;
    coAgentNumber?: null;
    infoString5?: null;
    agentPhoneNumber?: null;
    agentLicenseNumber?: null;
    providerLogo?: null;
    infoString16?: null;
    buyerBrokerageName?: null;
    mlsDisclaimer?: null;
    brokerPhoneNumber?: null;
    lastChecked?: null;
}

export interface ZillowPResoFacts {
    exteriorFeatures: string[] | null
    lotSize: string | null
    parcelNumber: string | null
    storiesDecimal: number | null
    roomTypes: string[] | null
    hasCooling: boolean | null
    parkingFeatures: string[] | null
    otherFacts: {
        value: string | null
        name: string | null
    }[] | null
    hasHeating: boolean | null
}

export interface ZillowPTaxRecord {
    time?: number | null;
    valueIncreaseRate?: number | null;
    taxIncreaseRate?: number | null;
    taxPaid?: number | null;
    value?: number | null;
}

export interface ZillowPListedBy {
    zpro?: string | null;
    display_name?: string | null;
    badge_type?: string | null;
    business_name?: string | null;
    rating_average?: number | null;
    phone?: string | null;
    zuid?: string | null;
    image_url?: string | null;
}

export interface ZillowPClimateData {
    windSources?: ZillowPClimateSource | null;
    floodSources?: ZillowPClimateSource | null;
    fireSources?: ZillowPClimateSource | null;
    heatSources?: ZillowPClimateSource | null;
    airSources?: ZillowPClimateSource | null;
}

export interface ZillowPClimateSource {
    primary?: ZillowPClimateRisk | null;
}

export interface ZillowPClimateRisk {
    insuranceRecommendation?: string | null;
    riskScore?: ZillowPRiskScore | null;
    probability?: ZillowPProbability[] | null;
    source?: ZillowPSource | null;
    insuranceSeparatePolicy?: string | null;
    historicCountAll?: number | null;
    percentile98Temp?: number | null;
    hotDays?: ZillowPHotDay[] | null;
    badAirDays?: ZillowPBadAirDay[] | null;
}

export interface ZillowPRiskScore {
    label?: string | null;
    max?: number | null;
    value?: number | null;
}

export interface ZillowPProbability {
    relativeYear?: number | null;
    probability?: number | null;
}

export interface ZillowPSource {
    url?: string | null;
}

export interface ZillowPHotDay {
    relativeYear?: number | null;
    dayCount?: number | null;
}

export interface ZillowPBadAirDay {
    relativeYear?: number | null;
    dayCount?: number | null;
}

export interface ZillowPPriceRecord {
    priceChangeRate?: number | null;
    date?: string | null;
    source?: string | null;
    postingIsRental?: boolean | null;
    time?: number | null;
    sellerAgent?: ZillowPAgent | null;
    showCountyLink?: boolean | null;
    attributeSource?: ZillowPAttributeSource | null;
    pricePerSquareFoot?: number | null;
    buyerAgent?: ZillowPAgent | null;
    event?: string | null;
    price?: number | null;
}

export interface ZillowPAgent {
    name?: string | null;
    photo?: string | null;
    profileUrl?: string | null;
}

export interface ZillowPAttributeSource {
    infoString1?: string | null;
    infoString2?: string | null;
    infoString3?: string | null;
}

export interface ZillowPListingProvider {
    enhancedVideoURL?: string | null;
    showNoContactInfoMessage?: boolean | null;
    postingGroupName?: string | null;
    isZRMSourceText?: boolean | null;
    showLogos?: boolean | null;
    sourceText?: string | null;
    title?: string | null;
    disclaimerText?: string | null;
    postingWebsiteURL?: string | null;
    agentLicenseNumber?: string | null;
    postingWebsiteLinkText?: string | null;
    enhancedDescriptionText?: string | null;
    agentName?: string | null;
}

export interface ZillowPMortgageRates {
    thirtyYearFixedRate?: number | null;
}

export interface ZillowPAddress {
    community?: string | null;
    city?: string | null;
    state?: string | null;
    neighborhood?: string | null;
    subdivision?: string | null;
    streetAddress?: string | null;
    zipcode?: string | null;
}

export interface ZillowPMortgageZHLRates {
    thirtyYearFixedBucket?: ZillowPMortgageRateBucket | null;
    fifteenYearFixedBucket?: ZillowPMortgageRateBucket | null;
    arm5Bucket?: ZillowPMortgageRateBucket | null;
}

export interface ZillowPMortgageRateBucket {
    rate?: number | null;
    rateSource?: string | null;
    lastUpdated?: number | null;
}

export interface GoogleGeocodeResponse {
    results: GoogleGeocodeResult[];
    status: string;
    error_message?: string;
}

export interface GoogleGeocodeResult {
    address_components: GoogleGeocodeAddressComponent[];
    formatted_address: string;
    geometry: {
        location: {
            lat: number;
            lng: number;
        };
        location_type: string;
        viewport: {
            northeast: { lat: number; lng: number };
            southwest: { lat: number; lng: number };
        };
    };
    place_id: string;
    types: string[];
}

export interface GoogleGeocodeAddressComponent {
    long_name: string;
    short_name: string;
    types: string[];
}

export interface GeoAddressObject {
    street: string;
    city: string;
    state: string;
    county: string;
    zip: string;
    lat: number;
    lng: number;
    geoAddress: string;
}
