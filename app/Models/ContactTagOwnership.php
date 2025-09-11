<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTagOwnership extends Model
{
    use HasFactory;

    protected $table = 'contact_tag_ownership';

    protected $fillable = [
        'contact_id',
        'tag_name',
        'user_id',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}