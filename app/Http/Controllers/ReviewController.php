<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user is enrolled
        $enrollment = Auth::user()->enrollments()->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return response()->json(['error' => 'You must be enrolled to review this course'], 403);
        }

        // Update or create review
        $review = Review::updateOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $course->id],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        // Update course average rating
        $course->updateRating();

        // Notify course author about new review
        \App\Models\Notification::create([
            'user_id' => $course->teacher_id,
            'title' => 'New Course Review',
            'message' => Auth::user()->name . ' rated your course "' . $course->title . '" ' . $request->rating . ' stars.',
            'type' => 'course_review',
            'data' => json_encode([
                'course_id' => $course->id,
                'rating' => $request->rating,
                'reviewer_name' => Auth::user()->name
            ])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $review->load('user')
        ]);
    }
}