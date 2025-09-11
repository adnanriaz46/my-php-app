<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyUnlistedList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'addresses',
        'remarks',
        'user_id',
    ];

    protected $casts = [
        'addresses' => 'array', // JSONB array of objects
    ];

    /**
     * Get the user that owns the list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
