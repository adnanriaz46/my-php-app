<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTrainingVideoProgress extends Model
{
    use HasFactory;

    protected $table = 'user_training_video_progress';

    protected $fillable = [
        'user_id',
        'training_video_id',
        'completed_at',
    ];

    protected $dates = [
        'completed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(TrainingVideo::class, 'training_video_id');
    }
} 