<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwsS3History extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'path',
        'url',
        'user_id',
        'errors',
    ];


    public static function addHistory($type, $path = null, $url = null, $userId = null, $error = null)
    {
        self::create(['type' => $type, 'path' => $path, 'url' => $url, 'user_id' => $userId, 'error' => $error]);
    }

    /**
     * Get the user that owns the AWS S3 history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
