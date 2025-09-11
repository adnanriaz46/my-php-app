<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedPdf extends Model
{
    protected $fillable = ['user_id', 'template_id', 'data_json', 'pdf_url'];

    protected $casts = [
        'data_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(PdfTemplate::class, 'template_id');
    }
} 