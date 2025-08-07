<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $enrollments = $user->enrollments()
            ->with(['course.lessons', 'course.category'])
            ->get();

        $stats = [
            'total_enrolled' => $enrollments->count(),
            'completed_courses' => $enrollments->where('progress_percentage', 100)->count(),
            'in_progress' => $enrollments->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100)->count(),
            'total_points' => $user->points ?? 0,
        ];

        return view('progress.index', compact('enrollments', 'stats'));
    }
}