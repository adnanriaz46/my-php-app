<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    protected $fillable = ['user_id', 'pdf_path', 'fields_json'];

    protected $casts = [
        'fields_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 