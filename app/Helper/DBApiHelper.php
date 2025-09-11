<?php

namespace App\Helper;

use App\Models\CountyState;
use App\Models\SuppressProperty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DBApiHelper
{
    public function getCalculatedData(Request $request)
    {
        $zip = $request->get('zip_code');
        $propertyType = $request->get('property_type');
        $lat = $request->get('latitude');
        $listPrice = $request->get('list_price');
        $lng = $request->get('longitude');
        $sqft = $request->get('sqft');
        $address = $request->get('address');

        return $this->HttpCallGet('sub-calls/get_calc_values.php', [
            'zip_code' => $zip,
            'property_type' => $propertyType,
            'latitude' => $lat,
            'longitude' => $lng,
            'sqft' => $sqft,
            'list_price' => $listPrice,
            'address' => $address,
        ]);
    }

    public function getMLSHistoryData(Request $request)
    {
        $address = $request->get('address');
        return $this->HttpCallGet('sub-calls/get_mls_by_address.php', ['address' => $address]);
    }


    public function getAveragePropertyCompsData(Request $request)
    {
        $propertyType = $request->get('property_type');
        if ($propertyType) {
            $propertyType = $this->arrayToString($propertyType);
        }

        $data = [
            'total_finished_sqft_min' => $request->get('sqft_min'),
            'total_finished_sqft_max' => $request->get('sqft_max'),
            'comps_sub_prop_id' => ($request->get('lat') . "|" . ($request->get('lng'))),
            'status' => 'Closed',
            'structure_type' => $propertyType,
            'closed_date_min' => $request->get('closed_on'),
            'zip' => $request->get('zip'),
            'distance_max' => $request->get('distance'),
        ];
        if ($request->get('ids')) {
            $ids = $request->get('ids');
            $data['filter_ids'] = $this->arrayToString($ids);
        }

        return $this->HttpCallGet('/sub-calls/get_comps_averages.php', $data);
    }

    private function preparePropertyRequestData(Request $request): array
    {
        $data = $request->input();

        $data['suppress_ids'] = isset($data['suppress_ids']) && is_array($data['suppress_ids'])
            ? $data['suppress_ids']
            : [];

        if (
            $request->user()?->id &&
            (!isset($data['ignore_default_user_suppress']) || $data['ignore_default_user_suppress'] == 0)
        ) {
            $userSuppressIds = SuppressProperty::where('user_id', $request->user()->id)
                ->pluck('property_id')
                ->toArray();

            $data['suppress_ids'] = array_unique(array_merge($data['suppress_ids'], $userSuppressIds));
        }

        if (empty($data['suppress_ids'])) {
            unset($data['suppress_ids']);
        }

        foreach ($data as $key => $datum) {
            if (is_array($datum)) {
                $data[$key] = implode(',', $datum);
            }
        }


        if (($request->get('status') && in_array('Closed', $request->get('status')))) {
            if (!$data['closed_date_min']) {
                $data['closed_date_min'] = Carbon::now()->subDays(150)->toDateString();
            }
        }


        return $data;
    }

    public function getPropertyMinimalListByText(Request $request)
    {
        $data = $request->input('query');

        $params = [
            'text' => $data
        ];
        if ($request->get('exclude_negative_status') == 'true') {
            $params['exclude_negative_status'] = 1;
        }

        return $this->HttpCallGet('/sub-calls/get_address_by_text.php', $params);
    }


    public function getPropertyListData(Request $request)
    {
        $data = $this->preparePropertyRequestData($request);

        //        if (!$request->get('_limit') || $request->get('_limit') > 500) {
//            $data['_limit'] = 500;
//        }

        return $this->HttpCallPost('get_properties_list.php', $data);
    }

    public function getPropertyCount(Request $request)
    {
        $data = $this->preparePropertyRequestData($request);

        return $this->HttpCallPost('get_properties_count.php', $data);
    }

    public function getPropertyData(Request $request)
    {
        $data = $this->preparePropertyRequestData($request);

        if (!$request->get('_limit') || $request->get('_limit') > 100) {
            $data['_limit'] = 50;
        }
        return $this->HttpCallPost('get_properties.php', $data);
    }

    public function getAskAiContent(Request $request)
    {
        $id = $request->input('id');
        return $this->HttpCallGet('/get_property_ai_content_by_id.php', ['id' => $id]);
    }


    public function getSoldUnderMarketValue(Request $request)
    {
        $range = $request->get('range');
        $state = $request->get('state');
        $county = $request->get('county');
        $city = $request->get('city');
        $zip = $request->get('zip');
        $limit = $request->get('limit') ?? 10;

        return $this->HttpCallGet('data_visualization/sold_under_market_activity.php', [
            'range' => $range,
            'state' => $state,
            'county' => $county,
            'city' => $city,
            'zip_code' => $zip,
            '_limit' => $limit,
        ]);
    }

    public function getSalesActivity(Request $request)
    {
        $range = $request->get('range');
        $state = $request->get('state');
        $county = $request->get('county');
        $city = $request->get('city');
        $zip = $request->get('zip');

        return $this->HttpCallGet('data_visualization/sale_activity.php', [
            'range' => $range,
            'state' => $state,
            'county' => $county,
            'city' => $city,
            'zip_code' => $zip
        ]);
    }

    public function getStatusActivity(Request $request)
    {

        $state = $request->get('state');
        $county = $request->get('county');
        $city = $request->get('city');
        $zip = $request->get('zip');

        return $this->HttpCallGet('data_visualization/status_activity_new.php', [

            'state' => $state,
            'county' => $county,
            'city' => $city,
            'zip_code' => $zip,
        ]);
    }

    private function HttpCallGet($path, $params)
    {
        $baseUrl = config('services.db_api.url');
        $apiKey = config('services.db_api.key');

        try {
            $response = Http::withHeaders(['API_KEY' => $apiKey])
                ->timeout(30) // Increased timeout to match cURL
                ->withOptions([
                    'allow_redirects' => true, // CURLOPT_FOLLOWLOCATION
                    'max_redirects' => 10, // CURLOPT_MAXREDIRS
                    'http_version' => '1.1', // CURLOPT_HTTP_VERSION
                    'verify' => false, // Disable SSL verification if needed
                    'force_ip_resolve' => 'v4' // Force IPv4
                ])
                ->get($baseUrl . $path, $params);

            if ($response->failed()) {
                \Log::error('DB API GET request failed', [
                    'url' => $baseUrl . $path,
                    'params' => $params,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);

                return response()->json([
                    'status' => 0,
                    'message' => 'Could not fetch the data!',
                    'details' => $response->json(),
                    'error_code' => $response->status()
                ], $response->status());
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            \Log::error('DB API GET request exception', [
                'url' => $baseUrl . $path,
                'params' => $params,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Network error occurred!',
                'details' => $e->getMessage(),
                'error_code' => 500
            ], 500);
        }
    }




    private function HttpCallPost($path, $params)
    {
        $baseUrl = config('services.db_api.url');
        $apiKey = config('services.db_api.key');

        try {
            $response = Http::withHeaders(['API_KEY' => $apiKey])->timeout(20)
                ->asMultipart()
                ->withOptions([
                    'force_ip_resolve' => 'v4',
                    'verify' => false
                ])
                ->post($baseUrl . $path, $params);

            if ($response->failed()) {
                \Log::error('DB API POST request failed', [
                    'url' => $baseUrl . $path,
                    'params' => $params,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);

                return response()->json([
                    'status' => 0,
                    'message' => 'Could not fetch the data!',
                    'details' => $response->json(),
                    'error_code' => $response->status()
                ], $response->status());
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            \Log::error('DB API POST request exception', [
                'url' => $baseUrl . $path,
                'params' => $params,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Network error occurred!',
                'details' => $e->getMessage(),
                'error_code' => 500
            ], 500);
        }
    }

    private function arrayToString($array)
    {
        $string = $array;
        if (is_array($array)) {
            $string = implode(',', $array);
        }

        return $string;
    }
    private function getFips($county, $state)
    {
        $row = CountyState::where('county', $county)->where('state', $state)->first();
        return $row ? $row->fips : '';
    }

    public function searchBuyers(Request $request)
    {

        $county = $request->get('county');
        $state = $request->get('state');
        $fips = $this->getFips($county, $state);

        if (empty($fips)) {
            $fips = '';
        }

        $address = $request->get('address');
        $params = [
            'address' => $request->get('address'),
            'zip' => $request->get('zip'),
            'lat' => $request->get('lat'),
            'lng' => $request->get('lng'),
            'range' => $request->get('range'),
            'days' => $request->get('days'),
            'fips' => $fips,

        ];


        return $this->HttpCallGet('/buyers/get_buyers_new.php', $params);
    }

    public function getOwnershipData(Request $request)
    {
        $address = $request->get('full_street_address');
        $city = $request->get('city');
        $state = $request->get('state');
        $zip = $request->get('zip');
        $county = $request->get('county');
        $county = trim(str_replace(' County', '', $county));

        if (empty($address) || empty($city) || empty($state) || empty($zip) || empty($county)) {
            return response()->json([
                'status' => 0,
                'message' => 'Missing required parameters',
                'data' => [],
                'count' => 0,
            ], 200);
        }

        $fips = $this->getFips($county, $state);

        if (empty($fips)) {
            $fips = '';
        }

        $params = [
            'full_street_address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'fips' => $fips,
        ];

        return $this->HttpCallGet('buyers/get_ownership_info.php', $params);
    }

    public function getDatatreePropertyInfo(Request $request)
    {
        $address = $request->get('full_street_address');
        $city = $request->get('city');
        $state = $request->get('state');
        $zip = $request->get('zip');
        $county = $request->get('county');
        $county = trim(str_replace(' County', '', $county));

        if (empty($address) || empty($city) || empty($state) || empty($zip) || empty($county)) {
            return response()->json([
                'status' => 0,
                'message' => 'Missing required parameters',
                'data' => [],
                'count' => 0,
            ], 200);
        }

        $fips = $this->getFips($county, $state);

        if (empty($fips)) {
            $fips = '';
        }

        $params = [
            'full_street_address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'fips' => $fips,
        ];

        return $this->HttpCallGet('buyers/get_property_info.php', $params);
    }


   
}
