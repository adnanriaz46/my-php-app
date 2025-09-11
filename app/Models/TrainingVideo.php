<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_post_id',
        'category',
        'subcategory',
        'topic',
        'summary',
        'summary_html',
        'embed_code',
        'youtube_video_id',
        'url',
        'transcript',
        'order',
    ];

    public function post()
    {
        return $this->belongsTo(TrainingPost::class, 'training_post_id');
    }

    public function userProgress()
    {
        return $this->hasMany(UserTrainingVideoProgress::class);
    }
} 