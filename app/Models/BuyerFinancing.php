<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyerFinancing extends Model
{

    protected $fillable = [
        'q1',
        'q2',
        'q3',
        'email',
        'first_name',
        'last_name',
        'phone',
        'property_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
