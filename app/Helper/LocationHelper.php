<?php

namespace App\Helper;

use App\Models\CountyState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationHelper
{
    public static function getComboboxCounties()
    {
        return CountyState::select(['display as name'])->get()->pluck('name');
    }

    public function getLocationPlaceData(Request $request)
    {
        $apiKey = env('GOOGLE_GEO_API');
        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json";

        $response = Http::get($url, [
            'input' => $request->input('search'),
            'language' => 'en',
            'types' => 'address',
            'components' => 'country:us',
            'key' => $apiKey
        ]);

        return $response->json();
    }


    public function getZillowLocationData(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $apiHost = config('services.zillow.host');
        $apiKey = config('services.zillow.key');
        $url = "https://zillow-com1.p.rapidapi.com/locationSuggestions";

        $response = Http::withHeaders([
            'x-rapidapi-host' => $apiHost,
            'x-rapidapi-key' => $apiKey
        ])->get($url, [
            'q' => $request->input('search'),
        ]);

        if ($response->body() !== null) {
            $body = json_decode($response->body(), true);
            if (isset($body['results'][0]['metaData'])) {
                return response()->json($body['results'][0]['metaData']);
            }
        }
        return null;
    }

    public function getZillowPropertyData(Request $request): ?\Illuminate\Http\JsonResponse
    {
        // Validate input
        $validated = $request->validate([
            'address' => 'required|string|max:500',
            'zid' => 'nullable|string|max:255',
        ]);

        $apiHost = config('services.zillow.host');
        $apiKey = config('services.zillow.key');
        $url = "https://zillow-com1.p.rapidapi.com/property";

        $response = Http::withHeaders([
            'x-rapidapi-host' => $apiHost,
            'x-rapidapi-key' => $apiKey,
        ])->get($url, [
            'address' => $validated['address'],
            'zID' => $validated['zid'] ?? '',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch data from Zillow.',
                'details' => $response->json(),
            ], $response->status());
        }

        return response()->json($response->json());
    }


    public static function getOwnershipDataOLD(Request $request)
    {

        $apiClient = config('services.microbilt.client');
        $apiSecret = config('services.microbilt.secret');

        $authUrl = 'https://api.microbilt.com/OAuth/Token';
        $oAuthData = [
            'client_id' => $apiClient,
            'client_secret' => $apiSecret,
            'grant_type' => 'client_credentials',
        ];

        $tokenResponse = Http::asForm()->post($authUrl, $oAuthData);

        if ($tokenResponse->failed()) {
            return response()->json([
                'error' => 'Failed to get OAuth token.',
                'details' => $tokenResponse->json(),
            ], $tokenResponse->status());
        }

        $accessToken = $tokenResponse->json()['access_token'];

        $url = "https://apitest.microbilt.com/PropertySearch/GetReport";

        $response = Http::withToken($accessToken)
            ->post($url, [
                'PropertyAddress' => [
                    'Addr1' => $request->get('address'),
                    'City' => $request->get('city'),
                    'StateProv' => $request->get('state'),
                    'PostalCode' => $request->get('zip'),
                ],
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch data from Microbilt.',
                'details' => $response->json(),
            ], $response->status());
        }

        return response()->json($response->json());
    }

    public static function getTestLocationData()
    {
        $json = '[
  { "id": 1, "name": "Alabama", "latitude": 32.806671, "longitude": -86.791130, "price": "1.2M" },
  { "id": 2, "name": "Alaska", "latitude": 61.370716, "longitude": -152.404419, "price": "560K" },
  { "id": 3, "name": "Arizona", "latitude": 33.729759, "longitude": -111.431221, "price": "980K" },
  { "id": 4, "name": "Arkansas", "latitude": 34.969704, "longitude": -92.373123, "price": "115K" },
  { "id": 5, "name": "California", "latitude": 36.116203, "longitude": -119.681564, "price": "2.1M" },
  { "id": 6, "name": "Colorado", "latitude": 39.059811, "longitude": -105.311104, "price": "425K" },
  { "id": 7, "name": "Connecticut", "latitude": 41.597782, "longitude": -72.755371, "price": "760K" },
  { "id": 8, "name": "Delaware", "latitude": 39.318523, "longitude": -75.507141, "price": "28K" },
  { "id": 9, "name": "Florida", "latitude": 27.766279, "longitude": -81.686783, "price": "1.1M" },
  { "id": 10, "name": "Georgia", "latitude": 33.040619, "longitude": -83.643074, "price": "870K" },
  { "id": 11, "name": "Hawaii", "latitude": 21.094318, "longitude": -157.498337, "price": "540K" },
  { "id": 12, "name": "Idaho", "latitude": 44.240459, "longitude": -114.478828, "price": "77K" },
  { "id": 13, "name": "Illinois", "latitude": 40.349457, "longitude": -88.986137, "price": "1.5M" },
  { "id": 14, "name": "Indiana", "latitude": 39.849426, "longitude": -86.258278, "price": "94K" },
  { "id": 15, "name": "Iowa", "latitude": 42.011539, "longitude": -93.210526, "price": "670K" },
  { "id": 16, "name": "Kansas", "latitude": 38.526600, "longitude": -96.726486, "price": "120K" },
  { "id": 17, "name": "Kentucky", "latitude": 37.668140, "longitude": -84.670067, "price": "390K" },
  { "id": 18, "name": "Louisiana", "latitude": 31.169546, "longitude": -91.867805, "price": "205K" },
  { "id": 19, "name": "Maine", "latitude": 44.693947, "longitude": -69.381927, "price": "580K" },
  { "id": 20, "name": "Maryland", "latitude": 39.063946, "longitude": -76.802101, "price": "1.8M" },
  { "id": 21, "name": "Massachusetts", "latitude": 42.230171, "longitude": -71.530106, "price": "2M" },
  { "id": 22, "name": "Michigan", "latitude": 43.326618, "longitude": -84.536095, "price": "770K" },
  { "id": 23, "name": "Minnesota", "latitude": 45.694454, "longitude": -93.900192, "price": "1.3M" }
]';
        return json_decode($json);
    }


    public function getComboboxSchoolDistricts(Request $request): \Illuminate\Http\JsonResponse
    {
        $sString = $request->get('s', '');
        if (empty($sString)) {
            return response()->json([]);
        }

        $jsonPath = public_path('data/school-district-data.json');
        if (!file_exists($jsonPath)) {
            return response()->json([]);
        }
        $districts = json_decode(file_get_contents($jsonPath), true);
        $matches = collect($districts)
            ->filter(function ($district) use ($sString) {
                return isset($district['name']) && stristr($district['name'], $sString);
            })
            ->pluck('name') // Get only the 'name'
            ->take(10) // Limit to 10 results
            ->values(); // Reindex the array

        return response()->json($matches);
    }
}
