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
        // Check if user is enrolled, lesson is free, or user is the course teacher
        $isEnrolled = Auth::check() && Auth::user()->enrollments()->where('course_id', $course->id)->exists();
        $isTeacher = Auth::check() && Auth::id() === $course->teacher_id;
        
        if (!$lesson->is_free && !$isEnrolled && !$isTeacher) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'You need to enroll in this course to access this lesson.');
        }

        // Check lesson progression - students must complete previous lessons
        if ($isEnrolled && !$isTeacher && $lesson->order > 1) {
            $previousLesson = $course->lessons()
                ->where('order', '<', $lesson->order)
                ->where('is_published', true)
                ->orderBy('order', 'desc')
                ->first();
            
            if ($previousLesson) {
                $previousProgress = LessonProgress::where('user_id', Auth::id())
                    ->where('lesson_id', $previousLesson->id)
                    ->where('is_completed', true)
                    ->first();
                
                if (!$previousProgress) {
                    return redirect()->route('lessons.show', [$course->slug, $previousLesson->slug])
                        ->with('error', 'You must complete the previous lesson first.');
                }
            }
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

        return view('lessons.show', compact('course', 'lesson', 'progress', 'nextLesson', 'previousLesson', 'isEnrolled', 'isTeacher'));
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
            $progress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Update enrollment progress
            $enrollment = Auth::user()->enrollments()->where('course_id', $lesson->course_id)->first();
            if ($enrollment) {
                $enrollment->recalculateProgress();
                
                // Check if course is fully completed (all lessons done)
                if ($enrollment->isFullyCompleted()) {
                    // Notify teacher for certificate approval
                    $lesson->course->teacher->notify(new \App\Notifications\SystemNotification([
                        'title' => 'Student Completed Course',
                        'message' => Auth::user()->name . ' has completed "' . $lesson->course->title . '" and is awaiting certificate approval.',
                        'type' => 'course_completion',
                        'course_id' => $lesson->course_id,
                        'student_id' => Auth::id(),
                        'student_name' => Auth::user()->name
                    ]));
                    
                    return response()->json([
                        'success' => true, 
                        'course_completed' => true,
                        'trigger_review' => true,
                        'course_id' => $lesson->course_id
                    ]);
                }
                
                // Trigger review modal at 80% progress
                if ($enrollment->progress_percentage >= 80) {
                    $hasReviewed = Auth::user()->reviews()->where('course_id', $lesson->course_id)->exists();
                    if (!$hasReviewed) {
                        return response()->json([
                            'success' => true,
                            'trigger_review' => true,
                            'course_id' => $lesson->course_id
                        ]);
                    }
                }

                // Award points
                Auth::user()->increment('points', 10);
            }
        }

        return response()->json(['success' => true]);
    }
}