<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLessonAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $lesson = $request->route('lesson');
        $user = Auth::user();

        if (!$user || !$user->isLearner()) {
            return $next($request);
        }

        // Check enrollment
        $enrollment = $user->enrollments()->where('course_id', $lesson->course_id)->first();
        if (!$enrollment) {
            return redirect()->route('courses.show', $lesson->course->slug)
                ->with('error', 'You must enroll in this course first.');
        }

        // Skip check for first lesson
        $previousLesson = $lesson->course->lessons()
            ->where('order', '<', $lesson->order)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousLesson) {
            $previousProgress = $previousLesson->userProgress($user->id);

            if ($previousProgress && !$previousProgress->is_completed) {
                $message = "You must complete the previous lesson '{$previousLesson->title}' first.";

                if ($previousLesson->require_quiz_pass && !$previousProgress->quiz_passed) {
                    $message .= " You must pass the quiz.";
                }
                if ($previousLesson->require_comment && !$previousProgress->comment_posted) {
                    $message .= " You must post a comment.";
                }

                return redirect()->route('courses.show', $lesson->course->slug)
                    ->with('error', $message);
            } elseif ($previousProgress && $previousProgress->is_completed && $previousProgress->pending_approval) {
                return redirect()->route('courses.show', $lesson->course->slug)
                    ->with('error', "The previous lesson '{$previousLesson->title}' is pending teacher approval.");
            }
        }

        return $next($request);
    }
}