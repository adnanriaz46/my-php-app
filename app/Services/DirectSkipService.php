<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MyBuyerListBuyer;
class DirectSkipService
{
    private $apiKey = 'X76M1PgqPJvelIKGSyIK682cac004aa65VHC63';
    private $baseUrl = 'https://api0.directskip.com/v2';

    public function searchContact($buyer)
    {
        try {
            // Extract LLC name
            $llcName = $buyer['investor_identifier'] ?? '';

            // Use ONLY MAILING ADDRESS - NO fallback to property address
            $mailingAddress = $buyer['MailingFullStreetAddress'] ?? '';
            $mailingCity = $buyer['MailingCity'] ?? '';
            $mailingState = $buyer['MailingState'] ?? '';
            $mailingZip = $buyer['MailingZIP5'] ?? '';

            // If no mailing address exists, we can't skip trace properly
            if (empty($mailingAddress) || empty($mailingCity) || empty($mailingState)) {
                Log::warning('No mailing address available for skip trace - cannot proceed', [
                    'llc_name' => $llcName,
                    'mailing_address' => $mailingAddress,
                    'mailing_city' => $mailingCity,
                    'mailing_state' => $mailingState,
                    'note' => 'Need mailing address to find LLC owner, not property address'
                ]);
                return null;
            }

            $requestData = [
                'api_key' => $this->apiKey,
                'last_name' => $llcName, // Use full LLC name like "REVAMP 365 LLC"
                'first_name' => '',
                'mailing_address' => $mailingAddress, // Use mailing address
                'mailing_city' => $mailingCity,
                'mailing_state' => $mailingState,
                'mailing_zip' => $mailingZip,
                'property_address' => '', // Keep empty
                'property_city' => '',
                'property_state' => '',
                'property_zip' => '',
                'custom_field_1' => 'custom1',
                'custom_field_2' => 'custom2',
                'custom_field_3' => 'custom3',
                'auto_match_boost' => 1,
                'dnc_scrub' => 0,
                'owner_fix' => 'Y' // This helps find the LLC owner
            ];

            Log::info('DirectSkip API request - Using mailing address for LLC owner lookup', [
                'llc_name' => $llcName,
                'mailing_address' => $mailingAddress,
                'mailing_city' => $mailingCity,
                'mailing_state' => $mailingState,
                'mailing_zip' => $mailingZip
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/search_contact.php', $requestData);

            if ($response->successful()) {
                $data = $response->json();
                $parsedData = $this->parseDirectSkipResponse($data);
                
                // Store the result in the shared SkipTraceResult table
                if ($parsedData) {
                    $skipTraceResult = \App\Models\SkipTraceResult::storeResult(
                        $llcName,
                        $mailingAddress,
                        $mailingCity,
                        $mailingState,
                        $mailingZip,
                        $parsedData
                    );
                    
                    // Add the skip_trace_result_id to the response
                    $parsedData['skip_trace_result_id'] = $skipTraceResult->id;
                }
                
                return $parsedData;
            } else {
                Log::error('DirectSkip API failed', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('DirectSkip API exception', [
                'error' => $e->getMessage(),
                'buyer' => $buyer
            ]);
            return null;
        }
    }
    public function checkExistingSkipTraceData($buyer)
    {
        // Use investor_identifier as the unique identifier for skip trace data
        // This ensures one skip trace record per LLC, regardless of mailing address
        $existingResult = \App\Models\SkipTraceResult::findExistingResult($buyer['investor_identifier']);

        if ($existingResult) {
            // Update usage count and last used timestamp
            $existingResult->update([
                'last_used_at' => now(),
                'usage_count' => $existingResult->usage_count + 1
            ]);

            return [
                'skip_trace_result_id' => $existingResult->id,
                'first_name' => $existingResult->first_name,
                'last_name' => $existingResult->last_name,
                'age' => $existingResult->age,
                'deceased' => $existingResult->deceased,
                'phone_numbers' => $existingResult->phone_numbers,
                'phone_type' => $existingResult->phone_type,
                'emails' => $existingResult->emails,
                'street' => $existingResult->street,
                'city' => $existingResult->city,
                'state' => $existingResult->state,
                'zip' => $existingResult->zip,
                'connected_people' => $existingResult->connected_people,
            ];
        }

        return null; // No existing data found
    }
    private function extractPersonNameFromLLC($llcName)
    {
        // Use the full LLC name as last_name (like your friend's example)
        return [
            'first_name' => '',
            'last_name' => $llcName // Use the complete LLC name, don't remove suffixes
        ];
    }

    private function parseDirectSkipResponse($data)
    {
        // Check if we have contacts in the response
        if (empty($data['contacts']) || !is_array($data['contacts'])) {
            return null;
        }

        // Get the first contact (most relevant)
        $firstContact = $data['contacts'][0];

        // Extract names from the first contact
        $names = $firstContact['names'] ?? [];
        $firstPerson = $names[0] ?? null;

        // Extract phones from the first contact
        $phones = $firstContact['phones'] ?? [];

        // Extract emails from the first contact
        $emails = $firstContact['emails'] ?? [];

        // Extract confirmed address from the first contact
        $confirmedAddress = $firstContact['confirmed_address'] ?? [];
        $address = $confirmedAddress[0] ?? null;

        // Extract relatives for connected people
        $relatives = $firstContact['relatives'] ?? [];

        // Parse the data
        $parsedData = [
            'first_name' => $firstPerson['firstname'] ?? null,
            'last_name' => $firstPerson['lastname'] ?? null,
            'age' => $firstPerson['age'] ? (int) $firstPerson['age'] : null,
            'deceased' => ($firstPerson['deceased'] ?? 'N') === 'Y',
            'phone_numbers' => [],
            'phone_type' => [],
            'emails' => [],
            'street' => $address['street'] ?? null,
            'city' => $address['city'] ?? null,
            'state' => $address['state'] ?? null,
            'zip' => $address['zip'] ?? null,
            'connected_people' => []
        ];

        // Extract phone numbers (up to 5)
        $phoneCount = 0;
        foreach ($phones as $phone) {
            if ($phoneCount >= 5)
                break;
            $parsedData['phone_numbers'][] = $phone['phonenumber'] ?? '';
            $parsedData['phone_type'][] = $phone['phonetype'] ?? '';
            $phoneCount++;
        }

        // Extract email addresses (up to 5)
        $emailCount = 0;
        foreach ($emails as $email) {
            if ($emailCount >= 5)
                break;
            $parsedData['emails'][] = $email['email'] ?? '';
            $emailCount++;
        }

        // Extract connected people (relatives)
        foreach ($relatives as $relative) {
            $parsedData['connected_people'][] = $relative['name'] ?? '';
        }

        // Check if we have meaningful data
        $hasData = !empty($parsedData['first_name']) ||
            !empty($parsedData['phone_numbers']) ||
            !empty($parsedData['emails']) ||
            !empty($parsedData['street']);

        if (!$hasData) {
            return null;
        }

        Log::info('DirectSkip API response parsed', [
            'found_person' => $parsedData['first_name'] . ' ' . $parsedData['last_name'],
            'has_phones' => !empty($parsedData['phone_numbers']),
            'has_emails' => !empty($parsedData['emails'])
        ]);

        return $parsedData;
    }
}