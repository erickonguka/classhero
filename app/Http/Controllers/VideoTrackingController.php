<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoTrackingController extends Controller
{
    public function updateProgress(Request $request, Lesson $lesson)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
            'audio_enabled' => 'required|boolean',
        ]);

        $progress = LessonProgress::firstOrCreate([
            'user_id' => Auth::id(),
            'lesson_id' => $lesson->id,
        ]);

        $watchedSeconds = (int) $request->current_time;
        $totalDuration = (int) $request->duration;
        
        // Update progress
        $progress->update([
            'video_watched_seconds' => max($progress->video_watched_seconds, $watchedSeconds),
            'audio_enabled' => $request->audio_enabled,
            'video_completed' => $watchedSeconds >= ($totalDuration * 0.95), // 95% completion
        ]);

        // Check if lesson is completed based on requirements
        $reviewTrigger = $this->checkLessonCompletion($lesson, $progress);
        
        $response = ['success' => true];
        if ($reviewTrigger) {
            $response = array_merge($response, $reviewTrigger);
        }

        return response()->json($response);
    }

    private function checkLessonCompletion(Lesson $lesson, LessonProgress $progress)
    {
        $completed = true;

        if ($lesson->require_video_completion && !$progress->video_completed) {
            $completed = false;
        }

        if ($lesson->require_quiz_pass && !$progress->quiz_passed) {
            $completed = false;
        }

        if ($lesson->require_comment && !$progress->comment_posted) {
            $completed = false;
        }

        if ($completed && !$progress->is_completed) {
            $progress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
            
            // Check if review modal should be triggered
            return $this->checkReviewTrigger($lesson, Auth::id());
        }
    }

    private function checkReviewTrigger(Lesson $lesson, $userId)
    {
        $course = $lesson->course;
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        // Check if user already reviewed
        $hasReviewed = $course->reviews()->where('user_id', $userId)->exists();
        
        if (!$hasReviewed) {
            $shouldTrigger = false;
            
            if ($totalLessons == 1 && $completedLessons == 1) {
                $shouldTrigger = true;
            } elseif ($totalLessons > 1 && ($completedLessons == 2 || $completedLessons == 3)) {
                $shouldTrigger = true;
            }
            
            if ($shouldTrigger) {
                return [
                    'success' => true,
                    'trigger_review' => true,
                    'course_id' => $course->id
                ];
            }
        }
        
        return null;
    }
}