<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'thumbnail',
        'eligible_user_types',
        'hide',
    ];

    protected $casts = [
        'eligible_user_types' => 'array',
        'hide' => 'boolean',
    ];

    public function videos()
    {
        return $this->hasMany(TrainingVideo::class);
    }

    public function userProgress()
    {
        return $this->hasManyThrough(UserTrainingVideoProgress::class, TrainingVideo::class, 'training_post_id', 'training_video_id', 'id', 'id');
    }
} 