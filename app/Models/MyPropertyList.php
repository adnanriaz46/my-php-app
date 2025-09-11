<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyPropertyList extends Model
{
    protected $fillable = [
        'name',
        'property_ids',
        'remarks',
        'user_id',
    ];

    protected $casts = [
        'property_ids' => 'array', // Laravel handles JSON/JSONB this way
    ];

    /**
     * Get the user that owns the list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
