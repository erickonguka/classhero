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
        'current_time' => 'nullable|numeric|min:0',
        'duration' => 'nullable|numeric|min:1',
        'audio_enabled' => 'nullable|boolean',
    ]);

    $userId = Auth::id();

    $progress = LessonProgress::firstOrCreate([
        'user_id' => $userId,
        'lesson_id' => $lesson->id,
    ]);

    // Only update if data is provided (optional tracking)
    if ($request->filled('current_time') && $request->filled('duration')) {
        $watchedSeconds = (int) $request->current_time;
        $totalDuration = (int) $request->duration;
        $watchedPercentage = $totalDuration > 0 ? round(($watchedSeconds / $totalDuration) * 100) : 0;

        $progress->update([
            'video_watched_seconds' => $watchedSeconds,
            'audio_enabled' => $request->boolean('audio_enabled'),
            // Remove video_completed update
        ]);

        // Optional response for analytics
        $watchedTimeFormatted = gmdate('i:s', $watchedSeconds);
        $totalDurationFormatted = gmdate('i:s', $totalDuration);
        $response = [
            'success' => true,
            'watched_percentage' => $watchedPercentage,
            'watched_time' => $watchedTimeFormatted,
            'total_duration' => $totalDurationFormatted,
            'message' => "You have watched {$watchedTimeFormatted} ({$watchedPercentage}%) of {$totalDurationFormatted} (optional).",
        ];
    } else {
        $response = ['success' => true, 'message' => 'No video progress to track.'];
    }

    // Check for review trigger without video completion
    $reviewTrigger = $this->checkLessonCompletion($lesson, $progress);
    if ($reviewTrigger) {
        $response = array_merge($response, $reviewTrigger);
    }

    return response()->json($response);
}

private function checkLessonCompletion(Lesson $lesson, LessonProgress $progress)
{
    $completed = true;

    // Remove video completion check
    // if ($lesson->require_video_completion && !$progress->video_completed) {
    //     $completed = false;
    // }

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

        // Update enrollment progress
        $enrollment = $lesson->course->enrollments()->where('user_id', Auth::id())->first();
        if ($enrollment) {
            $totalLessons = $lesson->course->lessons()->count();
            $completedLessons = $lesson->course->lessons()
                ->whereHas('progress', fn($query) => $query->where('user_id', Auth::id())->where('is_completed', true))
                ->count();
            $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            $enrollment->update([
                'lessons_completed' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress_percentage' => $progressPercentage,
                'completed_at' => $progressPercentage === 100 ? now() : null,
            ]);
        }

        return $this->checkReviewTrigger($lesson, Auth::id());
    }

    return null;
}

    private function checkReviewTrigger(Lesson $lesson, $userId)
    {
        $course = $lesson->course;
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

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
                    'course_id' => $course->id,
                ];
            }
        }

        return null;
    }
}