<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesaleProperty extends Model
{
    protected $fillable = [
        'lat',
        'lng',
        'baths',
        'beds',
        'structure_type',
        'total_finished_sqft',
        'lot_sqft',
        'year_built',
        'building_units_total',
        'cool_type',
        'heat_type',
        'tax_annual_amount',
        'tax_assessed_value',
        'tax_id_number',
        'zoning',
        'full_street_address',
        'city_name',
        'state_or_province',
        'zip_code',
        'geo_address',
        'county',
        'status',
        'list_price',
        'closed_date',
        'images',
        'hoa',
        'listing_agent',
        'listing_agent_email',
        'listing_office',
        'school_district',
        'municipality',
        'remarks_public',
        'rental_estimate',
        'seller_arv',
        'seller_avg_rent',
        'seller_avm',
        'seller_est_cashflow',
        'seller_est_flip_profit',
        'seller_est_flip_rehab',
        'seller_est_rental_rehab',
        'database_id',
        'show_full_address',
        'manual_address',
        'approved',
        'slug',
        'user_id',
        'closed_price'
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'baths' => 'decimal:1',
        'beds' => 'decimal:1',
        'images' => 'array',
        'show_full_address' => 'boolean',
        'hoa' => 'boolean',
        'manual_address' => 'boolean',
        'approved' => 'boolean',
        'rental_estimate' => 'decimal:2',
        'seller_arv' => 'decimal:2',
        'seller_avg_rent' => 'decimal:2',
        'seller_avm' => 'decimal:2',
        'seller_est_cashflow' => 'decimal:2',
        'seller_est_flip_profit' => 'decimal:2',
        'seller_est_flip_rehab' => 'decimal:2',
        'seller_est_rental_rehab' => 'decimal:2',
        'closed_date' => 'datetime:Y-m-d H:i:s',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewHistories()
    {
        return $this->hasMany(PropertyViewHistory::class, 'property_id', 'database_id');
    }

    public function viewAddressRequests()
    {
        return $this->hasMany(AddressRequest::class, 'property_id', 'database_id');
    }
}
