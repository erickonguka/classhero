<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        
        // Basic stats
        $totalCourses = Course::where('teacher_id', $teacherId)->count();
        $totalStudents = Enrollment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
        
        $totalRevenue = Payment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('status', 'completed')->sum('amount');
        
        // Course performance
        $courseStats = Course::where('teacher_id', $teacherId)
            ->withCount(['enrollments', 'lessons'])
            ->with(['reviews'])
            ->get()
            ->map(function($course) {
                return [
                    'title' => $course->title,
                    'enrollments' => $course->enrollments_count,
                    'lessons' => $course->lessons_count,
                    'rating' => $course->reviews->avg('rating') ?? 0,
                    'revenue' => $course->is_free ? 0 : ($course->enrollments_count * $course->price * 0.7)
                ];
            });

        // Monthly enrollment trends
        $monthlyEnrollments = Enrollment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month')
        ->toArray();

        // Fill missing months with 0
        $enrollmentData = [];
        for ($i = 1; $i <= 12; $i++) {
            $enrollmentData[] = $monthlyEnrollments[$i] ?? 0;
        }

        // Recent activity
        $recentEnrollments = Enrollment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->with(['user', 'course'])
        ->latest()
        ->take(10)
        ->get();

        return view('teacher.analytics.index', compact(
            'totalCourses',
            'totalStudents', 
            'totalRevenue',
            'courseStats',
            'enrollmentData',
            'recentEnrollments'
        ));
    }
}