<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'investment_strategy',
        'counties_invest',
        'property_types',
        'arv_min',
        'arv_max',
        'bath_min',
        'bath_max',
        'bed_min',
        'bed_max',
        'cashflow_min',
        'cashflow_max',
        'delta_psf_min',
        'delta_psf_max',
        'flip_profit_min',
        'flip_profit_max',
        'price_min',
        'price_max',
        'sqft_min',
        'sqft_max',
        'year_build_min',
        'year_build_max',
    ];

    protected $casts = [
        'investment_strategy' => 'array',
        'counties_invest' => 'array',
        'property_types' => 'array',
    ];



}
