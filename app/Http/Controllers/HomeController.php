<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::where('status', 'published')
            ->with(['teacher', 'category', 'reviews', 'enrollments'])
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->orderBy('enrollments_count', 'desc')
            ->take(6)
            ->get()
            ->map(function($course) {
                $course->enrolled_count = $course->enrollments_count;
                $course->rating = $course->reviews_avg_rating ?? 0;
                return $course;
            });

        $categories = Category::where('is_active', true)
            ->withCount('courses')
            ->take(8)
            ->get();

        $stats = [
            'total_courses' => Course::where('status', 'published')->count(),
            'total_students' => User::where('role', 'learner')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_enrollments' => \App\Models\Enrollment::count(),
        ];

        return view('home', compact('featuredCourses', 'categories', 'stats'));
    }
}