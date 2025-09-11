<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkipTraceResult extends Model
{
    protected $fillable = [
        'investor_identifier',
        'mailing_address',
        'mailing_city',
        'mailing_state',
        'mailing_zip',
        'first_name',
        'last_name',
        'age',
        'deceased',
        'phone_numbers',
        'phone_type',
        'emails',
        'street',
        'city',
        'state',
        'zip',
        'connected_people',
        'last_used_at',
        'usage_count'
    ];

    protected $casts = [
        'age' => 'integer',
        'deceased' => 'boolean',
        'phone_numbers' => 'array',
        'phone_type' => 'array',
        'emails' => 'array',
        'connected_people' => 'array',
        'last_used_at' => 'datetime:Y-m-d H:i:s',
        'usage_count' => 'integer'
    ];

    /**
     * Find existing skip trace result for the given investor_identifier
     */
    public static function findExistingResult($investorIdentifier)
    {
        return self::where('investor_identifier', $investorIdentifier)->first();
    }

    /**
     * Store or update skip trace result
     */
    public static function storeResult($investorIdentifier, $mailingAddress, $mailingCity, $mailingState, $mailingZip, $skipTraceData)
    {
        $result = self::findExistingResult($investorIdentifier);
        
        if ($result) {
            // Update existing record (update mailing address if different)
            $result->update([
                'mailing_address' => $mailingAddress,
                'mailing_city' => $mailingCity,
                'mailing_state' => $mailingState,
                'mailing_zip' => $mailingZip,
                'last_used_at' => now(),
                'usage_count' => $result->usage_count + 1
            ]);
            return $result;
        } else {
            // Create new record
            return self::create([
                'investor_identifier' => $investorIdentifier,
                'mailing_address' => $mailingAddress,
                'mailing_city' => $mailingCity,
                'mailing_state' => $mailingState,
                'mailing_zip' => $mailingZip,
                'first_name' => $skipTraceData['first_name'] ?? null,
                'last_name' => $skipTraceData['last_name'] ?? null,
                'age' => $skipTraceData['age'] ?? null,
                'deceased' => $skipTraceData['deceased'] ?? false,
                'phone_numbers' => $skipTraceData['phone_numbers'] ?? null,
                'phone_type' => $skipTraceData['phone_type'] ?? null,
                'emails' => $skipTraceData['emails'] ?? null,
                'street' => $skipTraceData['street'] ?? null,
                'city' => $skipTraceData['city'] ?? null,
                'state' => $skipTraceData['state'] ?? null,
                'zip' => $skipTraceData['zip'] ?? null,
                'connected_people' => $skipTraceData['connected_people'] ?? null,
                'last_used_at' => now(),
                'usage_count' => 1
            ]);
        }
    }
} 