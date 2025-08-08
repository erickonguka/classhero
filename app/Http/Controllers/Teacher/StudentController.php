<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::whereHas('course', function($q) {
            $q->where('teacher_id', Auth::id());
        })->with(['user', 'course']);

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $enrollments = $query->paginate(20);
        $courses = Auth::user()->courses;

        return view('teacher.students.index', compact('enrollments', 'courses'));
    }
}