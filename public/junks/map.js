$("#_l_map_container").html('<div id="_map_u"></div>');
const googleApiKey = 'AIzaSyBDNTzaUXUOeu3Fgf7w3Evsa2ADjx_Jmow';


let __condinates = [
    [40.13031, -75.12185, "301K", 1657484, "Active"], [40.44387934, -74.78817682, "2475K", 1478112, "Active Under Contract"], [39.35485, -76.51966, "325K", 1918738, "Canceled"], [38.71554, -75.1128, "70K", 1691076, "Closed"], [41.05352, -75.58716, "365K", 1926854, "Coming Soon"], [39.96324, -75.14332, "350K", 1708735, "Pending"], [38.91934, -77.08196, "325K", 1935591, "Temp Off Market"], [40.37117, -74.94724, "474K", 1935767, "Pending"], [39.09629822, -75.73421478, "175K", 1947559, "Active"], [38.5251, -75.07206, "399K", 1799264, "Active"], [38.84479, -75.38235, "558K", 1671725, "Active"], [39.91988, -76.93201, "60K", 1675541, "Canceled"], [39.30558, -76.67026, "49K", 1927111, "Pending"], [39.91946793, -75.15773773, "160K", 1723606, "Coming Soon"], [40.09392332, -76.72539955, "85K", 1716640, "Active"], [39.06234, -78.96905, "295K", 1732205, "Canceled"], [41.10915042, -75.66143195, "280K", 1481378, "Active"], [39.68713, -76.73954, "493K", 1739987, "Coming Soon"], [40.01766, -75.30206, "275K", 1882245, "Coming Soon"], [40.03386, -76.29828, "225K", 1682697, "Pending"], [39.29085, -76.63091, "170K", 1922078, "Coming Soon"], [38.054688, -78.764062, "775K", 1732445, "Pending"], [39.93836594, -75.16220856, "475K", 1715704, "Pending"], [39.29555, -76.58869, "255K", 1736561, "Active"], [39.19135, -75.51591, "140K", 1889414, "Active"], [39.03883, -77.03607, "998K", 1880420, "Active"], [40.05223, -74.84597, "130K", 1740464, "Active"], [39.98761, -75.18087, "129K", 1935892, "Coming Soon"], [39.9538864, -75.5173611, "Custom", 0, "Closed"]
];
// let __condinates = [
//     [40.13031, -75.12185, "301K", 1657484, "Active"], [40.44387934, -74.78817682, "2475K", 1478112, "Active Under Contract"], [39.35485, -76.51966, "325K", 1918738, "Canceled"], [38.71554, -75.1128, "70K", 1691076, "Closed"], [41.05352, -75.58716, "365K", 1926854, "Coming Soon"], [39.96324, -75.14332, "350K", 1708735, "P/ending"], [38.91934, -77.08196, "325K", 1935591, "Temp Off Market"]
// ]
let __BinCords = [
    [40.13031, -75.12185, "301K"], [38.83736, -77.45811, "390K"], [40.44387934, -74.78817682, "2475K"], [39.35485, -76.51966, "325K"], [41.05352, -75.58716, "365K"], [39.96324, -75.14332, "350K"], [38.91934, -77.08196, "325K"], [40.37117, -74.94724, "474K"], [39.93438339, -75.19661713, "160K"], [39.09629822, -75.73421478, "175K"], [38.5251, -75.07206, "399K"], [38.84479, -75.38235, "558K"], [39.91988, -76.93201, "60K"], [39.30558, -76.67026, "49K"], [39.91946793, -75.15773773, "160K"], [40.09392332, -76.72539955, "85K"], [39.06234, -78.96905, "295K"], [41.10915042, -75.66143195, "280K"], [39.68713, -76.73954, "493K"], [40.01766, -75.30206, "275K"], [40.03386, -76.29828, "225K"], [39.29085, -76.63091, "170K"], [38.054688, -78.764062, "775K"], [39.93836594, -75.16220856, "475K"], [39.19135, -75.51591, "140K"], [39.03883, -77.03607, "998K"], [40.05223, -74.84597, "130K"], [39.98761, -75.18087, "129K"], [38.89900636, -77.05418807, "995K"], [37.920467, -78.95045172, "415K"], [39.9538864, -75.5173611, "Custom"]
];


const radiusLat = 38.83736;
const radiusLng = -77.45811;
const radiusMiles = 5;




const slugify = str =>
    str.toString()
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');

var __map = L.map('_map_u', {
    center: [38.83736, -77.45811],
    zoom: 8,
    maxZoom: 19
});

var __markers = L.markerClusterGroup();
var regularLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://openstreetmap.org/copyright">Revamp365</a>'
});

// var satelliteLayer = L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
//    maxZoom: 19,
//    attribution: '&copy; <a href="https://openstreetmap.org/copyright">Revamp365</a>'
// });

var satelliteLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    maxZoom: 19,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: '&copy; <a href="https://openstreetmap.org/copyright">Revamp365</a>'
});

// var satelliteLayerRoad = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}.png', {
//    //subdomains:['mt0','mt1','mt2','mt3'],
//    maxZoom: 20,
// });
var satelliteLayerRoad = L.tileLayer('http://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    maxZoom: 19,
});

regularLayer.addTo(__map);

if (radiusLat && radiusLng && radiusMiles) {
    const radiusMeters = radiusMiles * 1609.34;
    const circle = L.circle([radiusLat, radiusLng], {
        color: '#c8a02e',
        fillColor: '#c8a02e',
        fillOpacity: 0.2,
        radius: radiusMeters
    }).addTo(__map);

    var myIcon = L.divIcon({
        className: '_marker-parent',
        iconSize: 'auto',
        iconAnchor: [7, 7],
        html: "<center class='center-point-circle-map'></center>"
    });
    const marker = L.marker([radiusLat, radiusLng], { title: "Center Marker", riseOnHover: true, icon: myIcon }).addTo(__map);
}

var markerSizeAndPosition = 25;
$(__condinates).each(function (index, value) {
    var myIcon = L.divIcon({
        className: '_marker-parent',
        iconSize: 'auto',
        iconAnchor: [markerSizeAndPosition, 40],
        html: "<div class='_marker-set __" + slugify(value[4]) + "'>" + value[2] + "</div><div class='__triangle __" + slugify(value[4]) + "'></div>"
    });

    if (parseFloat(value[0]) != NaN && parseFloat(value[1]) != NaN) {
        let marker = L.marker({ lat: value[0], lng: value[1] }, { icon: myIcon });
        marker.on('click', function () {
            __map.panTo(marker.getLatLng());
            bubble_fn_open_marker_popup(value[3] + ',' + index);
        });
        __markers.addLayer(marker);
    }
});

__map.on('click dragstart zoomstart', function (e) {
    bubble_fn_close_marker_popup();
});

let highlightBox = null;
__map.on('click', function (e) {
    if (highlightBox) {
        __map.removeLayer(highlightBox);
    }
    let selectedAddress, selectedBounds, selectedPLatLng = null;
    var zoomLevel = __map.getZoom();
    if (zoomLevel >= 18) {
        _elat = e.latlng.lat;
        _elng = e.latlng.lng;

        $.get(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${e.latlng.lat},${e.latlng.lng}&key=${googleApiKey}`).done(function (value) {
            if (value?.results?.[0] !== undefined) {
                selectedAddress = value?.results?.[0].formatted_address;
                selectedBounds = value?.results?.[0].geometry?.bounds;
                selectedPLatLng = value?.results?.[0].geometry?.location;

                console.log("Clicked Point: ", e.latlng);
                console.log("Proprety Bound: ", selectedBounds);
                console.log("Value: ", value?.results?.[0]);

                if (selectedBounds) {
                    let InBounds = isPointInBounds(_elat, _elng, selectedBounds);

                    if (InBounds) {
                        const bounds = [
                            [selectedBounds.northeast.lat, selectedBounds.northeast.lng],
                            [selectedBounds.southwest.lat, selectedBounds.southwest.lng]
                        ];

                        highlightBox = L.rectangle(bounds, { color: "green", weight: 2 }).addTo(__map);
                        bubble_fn_open_unlisted_open(selectedAddress);
                    }
                } else {
                    let meterDis = getDistanceFromLatLng(_elat, _elng, selectedPLatLng.lat, selectedPLatLng.lng);
                    const bounds = [
                        [selectedPLatLng.lat - 0.00003, selectedPLatLng.lng - 0.00005],
                        [selectedPLatLng.lat + 0.00003, selectedPLatLng.lng + 0.00005]
                    ];
                    if (meterDis <= 8) {
                        bubble_fn_open_unlisted_open(selectedAddress);
                        highlightBox = L.rectangle(bounds, { color: "#ff7800", weight: 2 }).addTo(__map);
                    }
                }
            }
            __map.panTo((selectedPLatLng) ? selectedPLatLng : e.latlng);
        }).always(function () {
            // bubble_fn_open_unlisted_initial(false);
        });
    }

});

__map.on('zoomend', function () {
    var zoomLevel = __map.getZoom();

    if (zoomLevel >= 18) {
        if (!__map.hasLayer(satelliteLayer)) {
            __map.removeLayer(regularLayer);
            satelliteLayer.addTo(__map);
            setTimeout(function () {
                satelliteLayerRoad.addTo(__map);
            }, 1000);
        }
    } else {
        if (!__map.hasLayer(regularLayer)) {
            __map.removeLayer(satelliteLayer);
            __map.removeLayer(satelliteLayerRoad);
            regularLayer.addTo(__map);
        }
    }
});

__map.addLayer(__markers);
__map.fitBounds(__BinCords);

function isPointInBounds(lat, lng, bounds) {
    const { northeast, southwest } = bounds;
    const isLatInBounds = lat >= southwest.lat && lat <= northeast.lat;
    const isLngInBounds = lng >= southwest.lng && lng <= northeast.lng;
    return isLatInBounds && isLngInBounds;
}

function getDistanceFromLatLng(lat1, lng1, lat2, lng2) {
    const R = 6371000; // Radius of the Earth in kilometers
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLng = (lng2 - lng1) * (Math.PI / 180);

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distanceInMeters = R * c;

    return distanceInMeters;
}
