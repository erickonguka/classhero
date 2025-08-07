<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $courses = Course::where('teacher_id', Auth::id())
            ->with(['enrollments.user'])
            ->get();

        $students = collect();
        foreach ($courses as $course) {
            foreach ($course->enrollments as $enrollment) {
                $students->push([
                    'user' => $enrollment->user,
                    'course' => $course,
                    'enrollment' => $enrollment,
                ]);
            }
        }

        return view('teacher.students.index', compact('students'));
    }

    public function show(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $enrollments = $course->enrollments()
            ->with(['user', 'user.lessonProgress'])
            ->paginate(20);

        return view('teacher.students.show', compact('course', 'enrollments'));
    }

    public function ban(Enrollment $enrollment)
    {
        $course = $enrollment->course;
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $enrollment->update(['is_banned' => true]);
        return response()->json(['success' => true]);
    }

    public function unban(Enrollment $enrollment)
    {
        $course = $enrollment->course;
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $enrollment->update(['is_banned' => false]);
        return response()->json(['success' => true]);
    }
}