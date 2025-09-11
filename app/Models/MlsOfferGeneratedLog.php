<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlsOfferGeneratedLog extends Model
{
    protected $table = 'mls_offer_generated_logs';
    protected $fillable = [
        'body',
        'file_url',
        'file_name',
        'is_email',
        'email_error',
        'email_success',
        'email_template_id',
        'offer_description',
        'offer_price',
        'property_data',
        'property_id',
        'send_at',
        'sent_to',
        'subject',
        'pdf_template_id',
        'user_id',
    ];
    protected $casts = [
        'property_data' => 'array',
        'is_email' => 'boolean',
        'email_success' => 'boolean',
        'send_at' => 'datetime:Y-m-d H:i:s',
        'offer_price' => 'decimal:2',
    ];
} 