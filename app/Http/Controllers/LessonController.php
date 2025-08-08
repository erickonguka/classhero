<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        // Check if user is enrolled or lesson is free
        $isEnrolled = Auth::check() && Auth::user()->enrollments()->where('course_id', $course->id)->exists();
        
        if (!$lesson->is_free && !$isEnrolled) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'You need to enroll in this course to access this lesson.');
        }

        $lesson->load(['course', 'quiz.questions']);
        
        // Get user's progress for this lesson
        $progress = null;
        if (Auth::check()) {
            $progress = LessonProgress::firstOrCreate([
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id,
            ], [
                'started_at' => now(),
            ]);
        }

        // Get next and previous lessons
        $nextLesson = $course->lessons()
            ->where('order', '>', $lesson->order)
            ->where('is_published', true)
            ->orderBy('order')
            ->first();

        $previousLesson = $course->lessons()
            ->where('order', '<', $lesson->order)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();

        return view('lessons.show', compact('course', 'lesson', 'progress', 'nextLesson', 'previousLesson', 'isEnrolled'));
    }

    public function complete(Lesson $lesson)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $progress = LessonProgress::where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($progress && !$progress->is_completed) {
            $isLastLesson = $lesson->course->lessons()
                ->where('order', '>=', $lesson->order)
                ->where('is_published', true)
                ->count() === 1;

            $completed = true;

            // Check completion requirements for non-quiz lessons
            if ($lesson->require_quiz_pass && !$progress->quiz_passed) {
                $completed = false;
            }
            if ($lesson->require_comment && !$progress->comment_posted) {
                $completed = false;
            }

            if ($completed) {
                $progress->update([
                    'is_completed' => true,
                    'completed_at' => now(),
                    'pending_approval' => $isLastLesson ? true : false, // Set pending approval for last lesson
                ]);

                // Update enrollment progress
                $enrollment = Auth::user()->enrollments()->where('course_id', $lesson->course_id)->first();
                if ($enrollment) {
                    $totalLessons = $lesson->course->lessons()->where('is_published', true)->count();
                    $completedLessons = Auth::user()->lessonProgress()
                        ->whereHas('lesson', function($query) use ($lesson) {
                            $query->where('course_id', $lesson->course_id);
                        })
                        ->where('is_completed', true)
                        ->count();

                    $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                    $enrollment->update([
                        'progress_percentage' => $progressPercentage,
                        'lessons_completed' => $completedLessons,
                    ]);

                    // Award points
                    Auth::user()->increment('points', 10);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}