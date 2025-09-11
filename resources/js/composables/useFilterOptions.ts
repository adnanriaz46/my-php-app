import {readonly} from "vue";

export function useFilterOptions() {
    const status = readonly({
        active: "Active",
        activeUnderContract: "Active Under Contract",
        canceled: "Canceled",
        closed: "Closed",
        comingSoon: "Coming Soon",
        expired: "Expired",
        pending: "Pending",
        tempOffMarket: "Temp Off Market",
        withdrawn: "Withdrawn",

    });
    const structureType = readonly({
        detached: "Detached",
        endOfRowTownhouse: "End of Row/Townhouse",
        garageParkingSpace: "Garage/Parking Space",
        interiorRowTownhouse: "Interior Row/Townhouse",
        manufactured: "Manufactured",
        mobilePre1976: "Mobile Pre 1976",
        other: "Other",
        penthouseUnitFlatApartment: "Penthouse Unit/Flat/Apartment",
        twinSemiDetached: "Twin/Semi-Detached",
        unitFlatApartment: "Unit/Flat/Apartment",

    });
    const dealType = readonly({
        mls: {display: 'MLS', value: "MLS"},
        wholesale: {display: 'Wholesale', value: 'Wholesale'},
        both: {display: 'Both', value: ''},
    });


    const _inMileRages = [
        {key: '0.1', value: `0.1 Miles`},
        {key: '0.25', value: `0.25 Miles`},
        {key: '0.5', value: `0.5 Miles`},
        {key: '0.75', value: `0.75 Miles`},
        {key: '1', value: `1 Mile`}
    ];
    const _mileRages = Array.from({length: 50}, (_, i) => ({
        key: (i + 2).toString(),
        value: `${i + 2} Miles`
    }));


    const mailRanges = readonly([..._inMileRages, ..._mileRages]);

    return {status, structureType, dealType, mailRanges};
}

export const PropertyHeatTypesArray: string[] = [
    '90% Forced Air',
    'Baseboard',
    'Baseboard - Electric',
    'Baseboard - Hot Water',
    'Central',
    'Forced Air',
    'Heat Pump - Electric BackUp',
    'Heat Pump(s)',
    'Hot Water & Baseboard - Electric',
    'Summer/Winter Changeover',
    'Energy Star Heating System',
    'Programmable Thermostat',
    'Zoned',
    'Other',
    'Humidifier',
    'Heat Pump - Gas BackUp',
    'Radiator',
    'Wood Burn Stove',
    'Radiant',
    'Heat Pump - Oil BackUp',
    'Hot Water',
    'Ceiling',
    'Solar - Active',
    'Solar - Passive',
    'Solar Rough-In',
    'Electric Air Filter',
    'Gas',
    'Steam',
    'Solar On Grid',
    'Wall Unit',
    'Convector',
    'Electric',
    'Oil',
    'Floor Furnace',
    'Geothermal Heat Pump',
    'Propane',
    'Space Heater',
    'Gravity',
    'Coal',
    'Solar Active/Passive',
    'Geothermal',
    'None',
    'Bio Fuel',
    'Solar Off Grid',
];

export const PropertyCoolTypesArray: string[] = [
    'Air Purification System',
    'Attic Fan',
    'Ceiling Fan(s)',
    'Central A/C',
    'Dehumidifier',
    'Ductless/Mini-Split',
    'Energy Star Cooling System',
    'Heat Pump(s)',
    'Programmable Thermostat',
    'Other',
    'Zoned',
    'Wall Unit',
    'Solar On Grid',
    'Whole House Fan',
    'Solar Attic Fan',
    'Fresh Air Recovery System',
    'Multi Units',
    'Roof Mounted',
    'Window Unit(s)',
    'Whole House Exhaust Ventilation',
    'Whole House Supply Ventilation',
    'Geothermal',
    'None',
    'Convector',
    'Solar Off Grid',
    'Solar Rough-In',
    'HRV/ERV'
];

export function getLimitedMailRanges(maxMiles: number) {
    const {mailRanges} = useFilterOptions()
    return mailRanges.filter(range => parseFloat(range.key) <= maxMiles)
}

export function objectToSelectArray(obj: object) {
    return Object.entries(obj).map(([key, value]) => ({key, value}));
}

export function objectToSelectArraySame(obj: object) {
    return Object.values(obj).map(value => ({key: value, value}));
}


export function objectToStringArray(obj: object) {
    return Object.values(obj).map(value => value);
}
