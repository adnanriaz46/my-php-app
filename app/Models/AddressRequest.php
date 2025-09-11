<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressRequest extends Model
{

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;
    protected $appends = ['status_label'];

    protected $fillable = [
        'is_agreed',
        'property_id',
        'wholesale_id',
        'remark',
        'user_id',
    ];

    protected $casts = [
        'is_agreed' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wholesale()
    {
        return $this->belongsTo(WholesaleProperty::class);

    }
    public function getStatusLabelAttribute(): string
    {
        return match ($this->is_agreed) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Pending',
        };
    }
}
