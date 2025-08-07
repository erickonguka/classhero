<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'learner') {
            $enrollments = $user->enrollments()
                ->with(['course.teacher', 'course.category'])
                ->latest()
                ->paginate(12);
                
            return view('my-courses.index', compact('enrollments'));
        }
        
        return redirect()->route('dashboard');
    }
}