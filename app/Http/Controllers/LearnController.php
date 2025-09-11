<?php
namespace App\Http\Controllers;

use App\Models\TrainingPost;
use App\Models\TrainingVideo;
use App\Models\UserTrainingVideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearnController extends Controller
{
    // List all training posts (for grid)
    public function index($slug = null)
    {
        $posts = TrainingPost::withCount('videos')->where('hide', false)->get();
        // Calculate completed_percent for each post if user is logged in
        $userId = \Auth::id();
        if ($userId) {
            foreach ($posts as $post) {
                $videoIds = $post->videos->pluck('id');
                $completed = \App\Models\UserTrainingVideoProgress::where('user_id', $userId)
                    ->whereIn('training_video_id', $videoIds)
                    ->count();
                $total = $post->videos_count;
                $post->completed_percent = $total ? round($completed / $total * 100) : 0;
            }
        } else {
            foreach ($posts as $post) {
                $post->completed_percent = 0;
            }
        }
        return \Inertia\Inertia::render('learn/Learn', [
            'posts' => $posts,
            'slug' => $slug,
        ]);
    }

    // Show a single training post with videos and user progress
    public function getData($id)
    {
        $post = TrainingPost::with([
            'videos' => function ($q) {
                $q->orderBy('order');
            }
        ])->findOrFail($id);
        $userId = Auth::id();
        $progress = [];
        if ($userId) {
            $progress = UserTrainingVideoProgress::where('user_id', $userId)
                ->whereIn('training_video_id', $post->videos->pluck('id'))
                ->pluck('training_video_id')
                ->toArray();
        }
        return response()->json([
            'post' => $post,
            'completed_videos' => $progress,
        ]);
    }

    // Mark a video as completed for the current user
    public function completeVideo(Request $request, $videoId)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $video = TrainingVideo::findOrFail($videoId);
        $progress = UserTrainingVideoProgress::firstOrCreate([
            'user_id' => $userId,
            'training_video_id' => $video->id,
        ]);
        $progress->completed_at = now();
        $progress->save();
        return response()->json(['success' => true]);
    }
}