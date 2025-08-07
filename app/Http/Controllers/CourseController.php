<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('status', 'published')
            ->with(['teacher', 'category']);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Difficulty filter
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Price filter
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('is_free', true);
            } elseif ($request->price === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'popular');
        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('enrolled_count', 'desc');
        }

        $courses = $query->withCount('enrollments')->with(['reviews', 'enrollments' => function($q) {
            if (Auth::check()) {
                $q->where('user_id', Auth::id());
            }
        }])->paginate(12);
        
        // Update course stats
        foreach ($courses as $course) {
            $course->enrolled_count = $course->enrollments_count;
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->rating_count = $course->reviews->count();
        }
        
        $categories = Category::where('is_active', true)->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'category', 'lessons' => function($query) {
            $query->where('is_published', true)->orderBy('order');
        }, 'reviews.user', 'enrollments.user']);

        // Update course stats
        $course->enrolled_count = $course->enrollments()->count();
        $course->rating = $course->reviews->avg('rating') ?? 0;
        $course->rating_count = $course->reviews->count();

        $isEnrolled = false;
        $enrollment = null;
        $userReview = null;
        
        if (Auth::check()) {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();
            $isEnrolled = $enrollment !== null;
            
            $userReview = $course->reviews()->where('user_id', Auth::id())->first();
        }

        $relatedCourses = Course::where('status', 'published')
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->with(['teacher', 'category'])
            ->take(4)
            ->get();

        // Check if user can enroll (only learners)
        $canEnroll = Auth::check() && Auth::user()->role === 'learner' && !$isEnrolled;

        return view('courses.show', compact('course', 'isEnrolled', 'enrollment', 'relatedCourses', 'canEnroll', 'userReview'));
    }

    public function enroll(Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Prevent teachers and admins from enrolling
        if ($user->role !== 'learner') {
            return redirect()->back()->with('error', 'Only learners can enroll in courses.');
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        // For paid courses, redirect to payment
        if (!$course->is_free) {
            return redirect()->route('payment.checkout', $course);
        }

        // Create enrollment for free courses
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'total_lessons' => $course->lessons()->where('is_published', true)->count(),
        ]);

        // Update course enrolled count
        $course->increment('enrolled_count');

        return redirect()->route('courses.show', $course->slug)
            ->with('success', 'Successfully enrolled in the course!');
    }
}