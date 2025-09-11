import { createApp, nextTick } from "vue";
// @ts-expect-error - Leaflet types not properly configured
import L from 'leaflet';
import { DBApiBuyer, DBApiPropertyList, paramsDBApiGetProperty } from "@/types/DBApi";
import { MapPropertyThumb, MapUnlistedPropertyThumb } from "@/components/property-thumb";
import 'leaflet.markercluster';
import BuyerMapPopup from '@/pages/buyers/BuyerMapPopup.vue';
// import {useScreen} from "@/composables/useScreen";
import axios from "axios";

import 'leaflet.markercluster'
// const {isMobile} = useScreen()
let map: L.Map | null = null

const markerMap = new Map<string, L.Marker>()

export const initMap = async (propertyList: DBApiPropertyList[], scrollToFn: (propertyId: number) => void, filterValues?: paramsDBApiGetProperty) => {

    const mapElement = document.getElementById('map')
    if (!mapElement) return;

    await nextTick();

    if (map) {
        map.off()
        map.remove()
    }
    map = L.map(mapElement).setView([40.7, -74.0], 4)

    const regularLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
        maxZoom: 19,
        attribution: '&copy; Revamp365 inc'
    });

    const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 19,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Revamp365 inc'
    });

    const satelliteLayerRoad = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        maxZoom: 19,
    });

    if (!propertyList || propertyList.length === 0) return;


    regularLayer.addTo(map)

    const markers: any[] = []

    propertyList.forEach((property) => {
        const lat = parseFloat(String(property.latitude))
        const lng = parseFloat(String(property.longitude))

        if (isNaN(lat) || isNaN(lng)) {
            console.warn('Skipping invalid property:', property)
            return
        }

        const marker = L.marker([lat, lng], {
            title: property.address,
            icon: L.divIcon({
                className: '_marker-parent',
                iconAnchor: [20, 20],
                iconSize: 'auto',
                html: "<div class='_marker-set __" + slugify(property.status) + "'>" + property.short_list_price + "</div><div class='__triangle __" + slugify(property.status) + "'></div>"
            })
        })

        marker.on('click', async () => {
            await setMapPopup(marker, property);
            scrollToFn(property.id);
        })

        markerMap.set(property.id.toString(), marker)

        markers.push(marker)
    })

    map.on('zoomend', function () {
        const zoomLevel = map.getZoom();
        if (zoomLevel >= 18) {
            if (!map.hasLayer(satelliteLayer)) {
                map.removeLayer(regularLayer);
                satelliteLayer.addTo(map);
                setTimeout(function () {
                    satelliteLayerRoad.addTo(map);
                }, 1000);
            }
        } else {
            if (!map.hasLayer(regularLayer)) {
                map.removeLayer(satelliteLayer);
                map.removeLayer(satelliteLayerRoad);
                regularLayer.addTo(map);
            }
        }
    });


    let highlightBox: any = null;
    const googleApiKey = import.meta.env.VITE_GOOGLE_GEO_API;

    map.on('click', async function (e: any) {
        if (highlightBox) {
            map!.removeLayer(highlightBox);
        }
        let selectedAddress, selectedBounds, selectedPLatLng: any = null;
        const zoomLevel = map!.getZoom();
        if (zoomLevel >= 18) {
            const _elat = e.latlng.lat;
            const _elng = e.latlng.lng;

            const response = await axios.get(`https://maps.googleapis.com/maps/api/geocode/json`, {
                params: {
                    latlng: `${_elat},${_elng}`,
                    key: googleApiKey
                }
            });
            const value = response.data;

            if (value?.results?.[0] !== undefined) {
                selectedAddress = value?.results?.[0].formatted_address;
                selectedBounds = value?.results?.[0].geometry?.bounds;
                selectedPLatLng = value?.results?.[0].geometry?.location;
                if (selectedBounds) {
                    const InBounds = isPointInBounds(_elat, _elng, selectedBounds);

                    if (InBounds) {
                        const bounds = [
                            [selectedBounds.northeast.lat, selectedBounds.northeast.lng],
                            [selectedBounds.southwest.lat, selectedBounds.southwest.lng]
                        ];

                        highlightBox = L.rectangle(bounds as L.LatLngBoundsExpression, { color: "green", weight: 2 }).addTo(map!);
                        openUnlistedPopup(selectedAddress, e.latlng);
                    }
                } else {
                    const meterDis = getDistanceFromLatLng(_elat, _elng, selectedPLatLng.lat, selectedPLatLng.lng);
                    const bounds = [
                        [selectedPLatLng.lat - 0.00003, selectedPLatLng.lng - 0.00005],
                        [selectedPLatLng.lat + 0.00003, selectedPLatLng.lng + 0.00005]
                    ];
                    if (meterDis <= 8) {
                        openUnlistedPopup(selectedAddress, e.latlng);
                        highlightBox = L.rectangle(bounds as L.LatLngBoundsExpression, { color: "#ff7800", weight: 2 }).addTo(map!);
                    }
                }
            }
            // map.panTo((selectedPLatLng) ? selectedPLatLng : e.latlng);
        }
    });

    if (filterValues?.comps_sub_prop_id && filterValues?.comps_sub_prop_id?.split('|')?.length > 1) {
        const radiusLat = filterValues.comps_sub_prop_id?.split('|')?.[0];
        const radiusLng = filterValues.comps_sub_prop_id?.split('|')?.[1];
        const radiusMiles = filterValues?.distance_max;

        if (radiusLat && radiusLng && radiusMiles) {
            const radiusMeters = radiusMiles * 1609.34;
            L.circle([parseFloat(radiusLat), parseFloat(radiusLng)], {
                color: '#c8a02e', fillColor: '#c8a02e', fillOpacity: 0.2, radius: radiusMeters
            }).addTo(map);

            const myIcon = L.divIcon({
                className: '_marker-parent',
                iconSize: 'auto',
                iconAnchor: [7, 7],
                html: "<span class='center-point-circle-map'></span>"
            });
            L.marker([parseFloat(radiusLat), parseFloat(radiusLng)], {
                title: "Center Marker", riseOnHover: true, icon: myIcon
            }).addTo(map);
        }
    }

    const markerCluster = (L as any).markerClusterGroup()
    markers.forEach(marker => markerCluster.addLayer(marker))
    map.addLayer(markerCluster)

    const group = L.featureGroup(markers)
    map.fitBounds(group.getBounds())

}


export const initMapResuable = async (propertyList: DBApiPropertyList[], scrollToFn: (propertyId: number) => void, filterValues?: paramsDBApiGetProperty) => {

    const mapElement = document.getElementById('map')
    if (!mapElement) return;

    await nextTick();

    if (map) {
        map.off()
        map.remove()
    }
    map = L.map(mapElement).setView([40.7, -74.0], 4)

    const regularLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
        maxZoom: 19,
        attribution: '&copy; Revamp365 inc'
    });

    const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 19,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Revamp365 inc'
    });

    const satelliteLayerRoad = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        maxZoom: 19,
    });

    if (!propertyList || propertyList.length === 0) return;


    regularLayer.addTo(map)

    const markers: any[] = []

    propertyList.forEach((property) => {
        const lat = parseFloat(String(property.latitude))
        const lng = parseFloat(String(property.longitude))

        if (isNaN(lat) || isNaN(lng)) {
            console.warn('Skipping invalid property:', property)
            return
        }

        const marker = L.marker([lat, lng], {
            title: property.address,
            icon: L.divIcon({
                className: '_marker-parent',
                iconAnchor: [20, 20],
                iconSize: 'auto',
                html: "<div class='_marker-set __" + slugify(property.status) + "'>" + property.short_list_price + "</div><div class='__triangle __" + slugify(property.status) + "'></div>"
            })
        })

        marker.on('click', async () => {
            await setMapPopup(marker, property);
            scrollToFn(property.id);
        })

        markerMap.set(property.id.toString(), marker)

        markers.push(marker)
    })

    map.on('zoomend', function () {
        const zoomLevel = map.getZoom();
        if (zoomLevel >= 18) {
            if (!map.hasLayer(satelliteLayer)) {
                map.removeLayer(regularLayer);
                satelliteLayer.addTo(map);
                setTimeout(function () {
                    satelliteLayerRoad.addTo(map);
                }, 1000);
            }
        } else {
            if (!map.hasLayer(regularLayer)) {
                map.removeLayer(satelliteLayer);
                map.removeLayer(satelliteLayerRoad);
                regularLayer.addTo(map);
            }
        }
    });


    let highlightBox: any = null;
    const googleApiKey = import.meta.env.VITE_GOOGLE_GEO_API;

    map.on('click', async function (e: any) {
        if (highlightBox) {
            map!.removeLayer(highlightBox);
        }
        let selectedAddress, selectedBounds, selectedPLatLng: any = null;
        const zoomLevel = map!.getZoom();
        if (zoomLevel >= 18) {
            const _elat = e.latlng.lat;
            const _elng = e.latlng.lng;

            const response = await axios.get(`https://maps.googleapis.com/maps/api/geocode/json`, {
                params: {
                    latlng: `${_elat},${_elng}`,
                    key: googleApiKey
                }
            });
            const value = response.data;

            if (value?.results?.[0] !== undefined) {
                selectedAddress = value?.results?.[0].formatted_address;
                selectedBounds = value?.results?.[0].geometry?.bounds;
                selectedPLatLng = value?.results?.[0].geometry?.location;
                if (selectedBounds) {
                    const InBounds = isPointInBounds(_elat, _elng, selectedBounds);

                    if (InBounds) {
                        const bounds = [
                            [selectedBounds.northeast.lat, selectedBounds.northeast.lng],
                            [selectedBounds.southwest.lat, selectedBounds.southwest.lng]
                        ];

                        highlightBox = L.rectangle(bounds as L.LatLngBoundsExpression, { color: "green", weight: 2 }).addTo(map!);
                        openUnlistedPopup(selectedAddress, e.latlng);
                    }
                } else {
                    const meterDis = getDistanceFromLatLng(_elat, _elng, selectedPLatLng.lat, selectedPLatLng.lng);
                    const bounds = [
                        [selectedPLatLng.lat - 0.00003, selectedPLatLng.lng - 0.00005],
                        [selectedPLatLng.lat + 0.00003, selectedPLatLng.lng + 0.00005]
                    ];
                    if (meterDis <= 8) {
                        openUnlistedPopup(selectedAddress, e.latlng);
                        highlightBox = L.rectangle(bounds as L.LatLngBoundsExpression, { color: "#ff7800", weight: 2 }).addTo(map!);
                    }
                }
            }
            // map.panTo((selectedPLatLng) ? selectedPLatLng : e.latlng);
        }
    });

    if (filterValues?.comps_sub_prop_id && filterValues?.comps_sub_prop_id?.split('|')?.length > 1) {
        const radiusLat = filterValues.comps_sub_prop_id?.split('|')?.[0];
        const radiusLng = filterValues.comps_sub_prop_id?.split('|')?.[1];
        const radiusMiles = filterValues?.distance_max;

        if (radiusLat && radiusLng && radiusMiles) {
            const radiusMeters = radiusMiles * 1609.34;
            L.circle([parseFloat(radiusLat), parseFloat(radiusLng)], {
                color: '#c8a02e', fillColor: '#c8a02e', fillOpacity: 0.2, radius: radiusMeters
            }).addTo(map);

            const myIcon = L.divIcon({
                className: '_marker-parent',
                iconSize: 'auto',
                iconAnchor: [7, 7],
                html: "<span class='center-point-circle-map'></span>"
            });
            L.marker([parseFloat(radiusLat), parseFloat(radiusLng)], {
                title: "Center Marker", riseOnHover: true, icon: myIcon
            }).addTo(map);
        }
    }

    const markerCluster = (L as any).markerClusterGroup()
    markers.forEach(marker => markerCluster.addLayer(marker))
    map.addLayer(markerCluster)

    const group = L.featureGroup(markers)
    map.fitBounds(group.getBounds())

}



export const __initMapBuyer = async (
    buyerList: any[], // Use your DBApiBuyer[] type if available
    mapElement: HTMLElement | null,
    onMarkerClick?: (buyer: any) => void // why is this function ?
) => {
    if (!mapElement) return;

    await nextTick();

    // Remove any previous map instance
    if ((window as any)._buyerMap) {
        (window as any)._buyerMap.off();
        (window as any)._buyerMap.remove();
    }

    // Use Mapbox Streets for the tile layer
    const map = L.map(mapElement).setView([40.7, -74.0], 13);
    (window as any)._buyerMap = map;

    L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
        maxZoom: 19,
        attribution: '&copy; Revamp365 inc'
    }).addTo(map);

    if (!buyerList || buyerList.length === 0) return;

    const markers: L.Marker[] = [];

    buyerList.forEach((buyer) => {
        const lat = parseFloat(String(buyer.mrp_lat));
        const lng = parseFloat(String(buyer.mrp_lng));
        if (isNaN(lat) || isNaN(lng)) return;

        const marker = L.marker([lat, lng], {
            title: buyer.mrp_fullstreet,
            icon: L.divIcon({
                className: 'buyer-marker-badge',
                iconSize: 'auto',
                iconAnchor: [20, 20],
                html: "<div class='_marker-set __" + (buyer.mrp_sales_price && !isNaN(parseFloat(String(buyer.mrp_sales_price))) ? 'active' : 'pending') + "'>" + (buyer.mrp_sales_price ? formatPriceToK(buyer.mrp_sales_price) : 'N/A') + "</div><div class='__triangle __" + (buyer.mrp_sales_price && !isNaN(parseFloat(String(buyer.mrp_sales_price))) ? 'active' : 'pending') + "'></div>"
            })
        });

        // --- Vue Popup ---
        const popupNode = document.createElement('div');
        createApp(BuyerMapPopup, { buyer }).mount(popupNode);

        marker.bindPopup(popupNode, { className: 'buyer-popup' });

        marker.on('click', () => {
            if (onMarkerClick) onMarkerClick(buyer);
        });

        markers.push(marker);
    });

    // --- Cluster Markers ---
    // @ts-expect-error - L.markerClusterGroup is not typed
    const markerCluster = L.markerClusterGroup({
        iconCreateFunction: function (cluster: any) {
            const count = cluster.getChildCount();

            // Get all buyers in this cluster
            const buyers = cluster.getAllChildMarkers()
                .map((marker: any) => marker.buyer)
                .filter((buyer: any) => buyer && buyer.mrp_sales_price && !isNaN(parseFloat(String(buyer.mrp_sales_price))));

            // Calculate price statistics
            const prices = buyers
                .map((buyer: any) => parseFloat(String(buyer.mrp_sales_price)))
                .filter((price: number) => !isNaN(price));

            let displayText = count.toString();
            let color = '#666'; // Default gray
            const textColor = '#fff';

            if (prices.length > 0) {
                const avgPrice = prices.reduce((sum: number, price: number) => sum + price, 0) / prices.length;
                displayText = formatPriceToK(avgPrice);
                color = '#38d39f'; // Green when price exists
            } else {
                // Fallback to count if no valid prices
                displayText = count.toString();
                color = '#666'; // Gray when no price
            }

            // Size based on count
            const size = count > 100 ? 56 : count > 50 ? 52 : 48;
            const fontSize = count > 100 ? 16 : count > 50 ? 15 : 14;

            return L.divIcon({
                html: `<div style="background:${color};color:${textColor};border-radius:50%;width:${size}px;height:${size}px;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:${fontSize}px;box-shadow:0 2px 6px rgba(0,0,0,0.15);border:2px solid #fff;">${displayText}</div>`,
                className: 'custom-cluster-icon',
                iconSize: [size, size]
            });
        }
    });
    markers.forEach(marker => markerCluster.addLayer(marker));
    map.addLayer(markerCluster);

    // --- Fit map to markers ---
    if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds(), { padding: [40, 40] });
    }
};


const slugify = (str: string): string => {
    return str
        .toString()
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')      // Remove non-word characters
        .replace(/[\s_-]+/g, '-')      // Replace spaces/underscores/dashes with single dash
        .replace(/^-+|-+$/g, '')       // Remove leading/trailing dashes
}

export const focusMarker = async (property: DBApiPropertyList) => {
    const marker = markerMap.get(property.id.toString())
    if (marker && map) {
        map.setView(marker.getLatLng(), 18, { animate: false })
        await nextTick();
        await setMapPopup(marker, property);
    }
}


export const focusMarkerBuyer = async (buyer: DBApiBuyer) => {
    const marker = markerMap.get(buyer.investor_identifier.toString())
    if (marker && map) {
        map.setView(marker.getLatLng(), 18, { animate: false })
        await nextTick();
        await setMapPopupBuyer(marker, buyer);
    }
}

export const setMapPopup = async (marker: any, property: DBApiPropertyList) => {
    if (window.innerWidth < 768) return

    const popupNode = document.createElement('div')
    createApp(MapPropertyThumb, { property }).mount(popupNode)
    marker.bindPopup(popupNode);
    await nextTick();
    marker.openPopup()
}

export const setMapPopupBuyer = async (marker: any, buyer: DBApiBuyer) => {
    if (window.innerWidth < 768) return

    const popupNode = document.createElement('div')
    createApp(BuyerMapPopup, { buyer }).mount(popupNode)
    marker.bindPopup(popupNode);
    await nextTick();
    marker.openPopup()
}



function openUnlistedPopup(address: string, latlng: L.LatLng) {
    const popupNode = document.createElement('div')

    // Mount Vue 3 component with props
    createApp(MapUnlistedPropertyThumb, { address: address }).mount(popupNode)

    L.popup({
        closeButton: true,
        autoPan: true,
        className: 'custom-unlisted-popup',
        maxWidth: 300
    })
        .setLatLng(latlng)
        .setContent(popupNode)
        .openOn(map!)
}

function isPointInBounds(lat: number, lng: number, bounds: any) {
    const { northeast, southwest } = bounds;
    const isLatInBounds = lat >= southwest.lat && lat <= northeast.lat;
    const isLngInBounds = lng >= southwest.lng && lng <= northeast.lng;
    return isLatInBounds && isLngInBounds;
}

function getDistanceFromLatLng(lat1: number, lng1: number, lat2: number, lng2: number) {
    const R = 6371000; // Radius of the Earth in kilometers
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLng = (lng2 - lng1) * (Math.PI / 180);

    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) * Math.sin(dLng / 2) * Math.sin(dLng / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distanceInMeters = R * c;

    return distanceInMeters;
}

// Utility function to format price to K/M format
function formatPriceToK(price: number | string): string {
    const numPrice = parseFloat(String(price));
    if (isNaN(numPrice)) return '0K';

    if (numPrice >= 1000000) {
        return `${(numPrice / 1000000).toFixed(1)}M`;
    } else if (numPrice >= 1000) {
        return `${Math.round(numPrice / 1000)}K`;
    } else {
        return `${numPrice}K`;
    }
}

// Simple Leaflet map for unlisted properties
export const initUnlistedPropertyMap = async (mapElement: HTMLElement | null, lat: number, lng: number) => {
    if (!mapElement) {
        console.warn('Map element not found');
        return;
    }

    await nextTick();

    // Remove any previous map instance
    if ((window as any)._unlistedPropertyMap) {
        (window as any)._unlistedPropertyMap.off();
        (window as any)._unlistedPropertyMap.remove();
    }

    // Create new map
    const map = L.map(mapElement).setView([lat, lng], 15);
    (window as any)._unlistedPropertyMap = map;

    // Add tile layer
    L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
        maxZoom: 19,
        attribution: '&copy; Revamp365 inc'
    }).addTo(map);

    // Add marker
    L.marker([lat, lng], {
        title: 'Property Location',
        icon: L.divIcon({
            className: '_marker-parent',
            iconSize: 'auto',
            iconAnchor: [20, 20],
            html: `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" class="text-red-500">
		<path fill="currentColor" fill-rule="evenodd" d="M12 2c-4.418 0-8 4.003-8 8.5c0 4.462 2.553 9.312 6.537 11.174a3.45 3.45 0 0 0 2.926 0C17.447 19.812 20 14.962 20 10.5C20 6.003 16.418 2 12 2m0 10a2 2 0 1 0 0-4a2 2 0 0 0 0 4" clip-rule="evenodd" />
	</svg>`
        })
    }).addTo(map);
};
