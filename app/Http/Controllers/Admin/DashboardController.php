<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_learners' => User::where('role', 'learner')->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentCourses = Course::with('teacher')->latest()->take(5)->get();
        $topCourses = Course::orderBy('enrolled_count', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCourses', 'topCourses'));
    }

    public function users()
    {
        return redirect()->route('admin.users.index');
    }

    public function courses()
    {
        return redirect()->route('admin.courses.index');
    }

    public function categories()
    {
        return redirect()->route('admin.categories.index');
    }

    public function analytics()
    {
        // Monthly data for charts
        $monthlyEnrollments = [];
        $monthlyRevenue = [];
        $monthlyUsers = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthlyEnrollments[$i] = Enrollment::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();
                
            $monthlyRevenue[$i] = Payment::where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->sum('amount');
                
            $monthlyUsers[$i] = User::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        // Category statistics
        $categoryStats = Category::withCount(['courses' => function($query) {
            $query->where('status', 'published');
        }])
        ->with(['courses' => function($query) {
            $query->where('status', 'published');
        }])
        ->get()
        ->map(function($category) {
            return [
                'name' => $category->name,
                'course_count' => $category->courses_count,
                'total_enrollments' => $category->courses->sum('enrolled_count'),
                'total_revenue' => Payment::whereIn('course_id', $category->courses->pluck('id'))
                    ->where('status', 'completed')
                    ->sum('amount')
            ];
        });

        // Top performing courses
        $topCourses = Course::where('status', 'published')
            ->orderBy('enrolled_count', 'desc')
            ->take(10)
            ->get();

        // Recent activity
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.analytics', compact(
            'monthlyEnrollments', 
            'monthlyRevenue', 
            'monthlyUsers',
            'categoryStats', 
            'topCourses',
            'recentEnrollments'
        ));
    }
}