export interface MbOwnershipData {
    error?: string | null
    MsgRsHdr?: {
        RqUID?: string | null
        Status?: {
            StatusCode?: number | null
            Severity?: string | null
            StatusDesc?: string | null
        } | null
    } | null
    Request?: {
        MsgRqHdr?: {
            MemberId?: string | null
            MemberPwd?: string | null
            RefNum?: string | null
            UserName?: string | null
        } | null
        PropertyAddress?: {
            StreetType?: string | null
            StreetNum?: string | null
            StreetName?: string | null
            PreDirection?: string | null
            PostDirection?: string | null
            Addr2?: string | null
            Addr1?: string | null
            City?: string | null
            StateProv?: string | null
            PostalCode?: string | null
            County?: string | null
            AddrType?: string | null
            DropPointInd?: string | null
            Urbanization?: string | null
        } | null
    } | null
    PropertyMatchInd?: {
        CurrentOwnerNameMatch?: string | null
        CurrentOwnerSiteAddrMatch?: string | null
        SingleAPNMatch?: string | null
        UnitNumMatch?: string | null
        Source?: string | null
        EffDt?: string | null
    } | null
    PropertyReportSummary?: {
        DeedRecordsInd?: string | null
        AssessmentRecordsInd?: string | null
        MortgageRecordsInd?: string | null
        NODRecordsInd?: string | null
        Source?: string | null
        EffDt?: string | null
    } | null
    DeedRecord?: Array<{
        Buyer1?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
            ContactInfo?: Array<{
                PostAddr?: {
                    Addr1?: string | null
                    City?: string | null
                    StateProv?: string | null
                    PostalCode?: string | null
                    Source?: string | null
                    EffDt?: string | null
                } | null
            }> | null
        } | null
        Buyer2?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
        } | null
        RecordType?: string | null
        RecordingDt?: string | null
        DocumentNumber?: string | null
        SellerName?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
        } | null
        SaleLoanAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        APNPIN?: string | null
        TitleCompany?: string | null
        BuyerVestingCode?: string | null
        CityTransferTax?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        CountyTransferTax?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        DeedPID?: string | null
        OrigDtOfContract?: string | null
        LoanType?: string | null
        LenderName?: string | null
        LenderType?: string | null
        LoanValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        LoanInterestRate?: string | null
        DueDate?: string | null
        LegalBriefDesc?: string | null
    }> | null
    AssessmentRecord?: Array<{
        Owner1Name?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
                Source?: string | null
                EffDt?: string | null
            } | null
            ContactInfo?: Array<{
                PostAddr?: {
                    Addr1?: string | null
                    City?: string | null
                    StateProv?: string | null
                    PostalCode?: string | null
                    Source?: string | null
                    EffDt?: string | null
                } | null
            }> | null
            Source?: string | null
            EffDt?: string | null
        } | null
        Owner2Name?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
                Source?: string | null
                EffDt?: string | null
            } | null
            Source?: string | null
            EffDt?: string | null
        } | null
        FIPSCode?: string | null
        DocumentNumber?: string | null
        AssessedValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        APNPIN?: string | null
        Assessee1Addr?: {
            Addr1?: string | null
            City?: string | null
            StateProv?: string | null
            Source?: string | null
            EffDt?: string | null
        } | null
        Assessee2Addr?: {
            Addr1?: string | null
            City?: string | null
            StateProv?: string | null
            PostalCode?: string | null
            Source?: string | null
            EffDt?: string | null
        } | null
        PropertyTotalValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        TaxAmount?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        MarketImprovementValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        AssessedLandValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        MarketLandValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        TotalMktValueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        PriorSaleAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        PriorSaleDt?: string | null
        LandUsage?: string | null
        LegalBriefDesc?: string | null
        LotSize?: string | null
        NumBedrooms?: string | null
        NumFullBaths?: string | null
        Garage?: string | null
        HVAC?: string | null
        LegalBriefDescFull?: string | null
        AssessmentYear?: string | null
        NumBuildings?: string | null
    }> | null
    MortgageRecord?: Array<{
        Borrower1?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
            ContactInfo?: Array<{
                PostAddr?: {
                    Addr1?: string | null
                    Source?: string | null
                    EffDt?: string | null
                } | null
            }> | null
        } | null
        Borrower2?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
        } | null
        RecordType?: string | null
        RecordingDt?: string | null
        DocumentNumber?: string | null
        MortgageAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        APNPIN?: string | null
        LenderType?: string | null
        EquityCreditLine?: string | null
        SAMPID?: string | null
        OrigDtOfContract?: string | null
        TitleCompany?: string | null
        MaxInterestRate?: string | null
        DueDate?: string | null
        LoanNumber?: string | null
        LoanInterestRate?: string | null
    }> | null
    NODRecord?: Array<{
        Trustor1Name?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
            ContactInfo?: Array<{
                PostAddr?: {
                    Addr1?: string | null
                    City?: string | null
                    StateProv?: string | null
                    PostalCode?: string | null
                    Source?: string | null
                    EffDt?: string | null
                } | null
            }> | null
        } | null
        Trustor2Name?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
        } | null
        RecordingDt?: string | null
        DocumentNumber?: string | null
        Trustee?: {
            PersonName?: {
                LastName?: string | null
                FullName?: string | null
                FirstName?: string | null
            } | null
            ContactInfo?: Array<{
                PhoneNum?: {
                    PhoneType?: string | null
                    Phone?: string | null
                } | null
                PostAddr?: {
                    Addr1?: string | null
                    City?: string | null
                    StateProv?: string | null
                    PostalCode?: string | null
                    Source?: string | null
                    EffDt?: string | null
                } | null
            }> | null
        } | null
        SaleLoanAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        APNPIN?: string | null
        PastDueAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        OrigLenderName?: string | null
        OrigLenderType?: string | null
        CurrentLenderName?: string | null
        CurrentLenderType?: string | null
        LoanRecordingDt?: string | null
        LoanDocumentNumber?: string | null
        CaseNumber?: string | null
        OrigDtOfContract?: string | null
        DeedPID?: string | null
        FIPSCode?: string | null
        AuctionMinBidAmt?: {
            Amt?: number | null
            CurCode?: string | null
        } | null
        DueDate?: string | null
        OrigNODRecordingDt?: string | null
        OrigNODDocumentNumber?: string | null
        TrusteeSaleNum?: string | null
    }> | null
}



/// Backup: ZillowProperty Response for validation..
interface ZillowBackup {
    longitude: number;
    imgSrc: string;
    streetAddress: string;
    county: string;
    taxHistory?: (TaxHistoryEntity)[] | null;
    annualHomeownersInsurance: number;
    state: string;
    listed_by: ListedBy;
    yearBuilt: number;
    brokerageName?: null;
    isListedByOwner?: null;
    climate: Climate;
    latitude: number;
    priceHistory?: (PriceHistoryEntity)[] | null;
    rentZestimate: number;
    city: string;
    providerListingID?: null;
    currency: string;
    listingProvider: ListingProvider;
    propertyTaxRate: number;
    mortgageRates: MortgageRates;
    address: Address;
    cityId: number;
    timeOnZillow: string;
    url: string;
    zestimate: number;
    zpid: number;
    countyId: number;
    brokerId?: null;
    livingAreaUnits: string;
    comingSoonOnMarketDate?: null;
    bathrooms: number;
    building?: null;
    stateId: number;
    zipcode: string;
    zestimateLowPercent: string;
    price: number;
    attributionInfo: AttributionInfo;
    homeStatus: string;
    resoFacts: ResoFacts;
    datePosted: string;
    bedrooms: number;
    propertyTypeDimension: string;
    mortgageZHLRates: MortgageZHLRates;
    pageViewCount: number;
    zestimateHighPercent: string;
    mlsid?: null;
    description: string;
    livingArea: number;
    buildingId?: null;
    country: string;
    homeType: string;
}

interface TaxHistoryEntity {
    time: number;
    valueIncreaseRate: number;
    taxIncreaseRate: number;
    taxPaid: number;
    value: number;
}

interface ListedBy {
    zpro?: null;
    display_name?: null;
    badge_type?: null;
    business_name?: null;
    rating_average?: null;
    phone?: null;
    zuid?: null;
    image_url?: null;
}

interface Climate {
    windSources: WindSources;
    floodSources: FloodSources;
    fireSources: FireSources;
    heatSources: HeatSources;
    airSources: AirSources;
}

interface WindSources {
    primary: Primary;
}

interface Primary {
    insuranceRecommendation: string;
    riskScore: RiskScore;
    probability?: (ProbabilityEntity)[] | null;
    source: Source;
    insuranceSeparatePolicy: string;
    historicCountAll: number;
}

interface RiskScore {
    label: string;
    max: number;
    value: number;
}

interface ProbabilityEntity {
    relativeYear: number;
    probability: number;
}

interface Source {
    url: string;
}

interface FloodSources {
    primary: Primary1;
}

interface Primary1 {
    insuranceRecommendation: string;
    riskScore: RiskScore;
    femaZone: string;
    source: Source;
    probability?: (ProbabilityEntity)[] | null;
    insuranceSeparatePolicy: string;
    historicCountPropertyAll: number;
}

interface FireSources {
    primary: Primary2;
}

interface Primary2 {
    insuranceRecommendation: string;
    riskScore: RiskScore;
    source: Source;
    insuranceSeparatePolicy: string;
    historicCountAll: number;
}

interface HeatSources {
    primary: Primary3;
}

interface Primary3 {
    percentile98Temp: number;
    hotDays?: (HotDaysEntityOrBadAirDaysEntity)[] | null;
    riskScore: RiskScore;
    source: Source;
}

interface HotDaysEntityOrBadAirDaysEntity {
    relativeYear: number;
    dayCount: number;
}

interface AirSources {
    primary: Primary4;
}

interface Primary4 {
    source: Source;
    riskScore: RiskScore;
    badAirDays?: (HotDaysEntityOrBadAirDaysEntity)[] | null;
}

interface PriceHistoryEntity {
    priceChangeRate: number;
    date: string;
    source: string;
    postingIsRental: boolean;
    time: number;
    sellerAgent: SellerAgentOrBuyerAgent;
    showCountyLink: boolean;
    attributeSource: AttributeSource;
    pricePerSquareFoot: number;
    buyerAgent: SellerAgentOrBuyerAgent;
    event: string;
    price: number;
}

interface SellerAgentOrBuyerAgent {
    name: string;
    photo?: null;
    profileUrl: string;
}

interface AttributeSource {
    infoString2: string;
    infoString3?: null;
    infoString1: string;
}

interface ListingProvider {
    enhancedVideoURL?: null;
    showNoContactInfoMessage: boolean;
    postingGroupName?: null;
    isZRMSourceText?: null;
    showLogos?: null;
    sourceText?: null;
    title: string;
    disclaimerText?: null;
    postingWebsiteURL?: null;
    agentLicenseNumber?: null;
    postingWebsiteLinkText: string;
    enhancedDescriptionText?: null;
    agentName?: null;
}

interface MortgageRates {
    thirtyYearFixedRate: number;
}

interface Address {
    community?: null;
    city: string;
    state: string;
    neighborhood?: null;
    subdivision?: null;
    streetAddress: string;
    zipcode: string;
}

interface AttributionInfo {
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

interface ListingOfficesEntity {
    associatedOfficeType: string;
    officeName?: null;
}

interface ResoFacts {
    exteriorFeatures?: (string)[] | null;
    lotSize: string;
    parcelNumber: string;
    storiesDecimal?: null;
    roomTypes?: null;
    hasCooling: boolean;
    parkingFeatures?: (string)[] | null;
    otherFacts?: (OtherFactsEntity)[] | null;
    hasHeating: boolean;
}

interface OtherFactsEntity {
    value: string;
    name: string;
}

interface MortgageZHLRates {
    thirtyYearFixedBucket: ThirtyYearFixedBucketOrFifteenYearFixedBucketOrArm5Bucket;
    fifteenYearFixedBucket: ThirtyYearFixedBucketOrFifteenYearFixedBucketOrArm5Bucket;
    arm5Bucket: ThirtyYearFixedBucketOrFifteenYearFixedBucketOrArm5Bucket;
}

interface ThirtyYearFixedBucketOrFifteenYearFixedBucketOrArm5Bucket {
    rate: number;
    rateSource: string;
    lastUpdated: number;
}
