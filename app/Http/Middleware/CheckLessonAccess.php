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

        // Check if user is enrolled in the course
        $enrollment = $user->enrollments()->where('course_id', $lesson->course_id)->first();
        if (!$enrollment) {
            return redirect()->route('courses.show', $lesson->course->slug)
                ->with('error', 'You must enroll in this course first.');
        }

        // Check if previous lesson is completed (except for first lesson)
        $previousLesson = $lesson->course->lessons()
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousLesson) {
            $previousProgress = $previousLesson->userProgress($user->id);
            if (!$previousProgress || !$previousProgress->is_completed) {
                return redirect()->route('courses.show', $lesson->course->slug)
                    ->with('error', 'You must complete the previous lesson first.');
            }
        }

        return $next($request);
    }
}
