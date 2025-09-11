<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlsOfferPdfTemplate extends Model
{
    protected $table = 'mls_offer_pdf_templates';

    protected $fillable = [
        'additional_requests',
        'fillable',
        'folder_id',
        'folder_name',
        'template_id',
        'template_name',
        'user_id',
    ];
} 