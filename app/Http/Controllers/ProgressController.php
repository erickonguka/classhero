<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->enrollments()
            ->with(['course.lessons', 'course.category', 'course.teacher', 'certificateRelation']);

        // Filter by status
        if ($request->status) {
            switch ($request->status) {
                case 'completed':
                    $query->where('progress_percentage', 100);
                    break;
                case 'in_progress':
                    $query->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100);
                    break;
                case 'not_started':
                    $query->where('progress_percentage', 0);
                    break;
            }
        }

        // Filter by category
        if ($request->category_id) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Search
        if ($request->search) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sort = $request->get('sort', 'enrolled_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $enrollments = $query->paginate(12);
        
        // Recalculate progress for all enrollments
        foreach ($enrollments as $enrollment) {
            $enrollment->recalculateProgress();
        }

        $stats = [
            'total_enrolled' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->where('progress_percentage', 100)->count(),
            'in_progress' => $user->enrollments()->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100)->count(),
            'total_points' => $user->points ?? 0,
        ];

        $categories = Category::whereHas('courses.enrollments', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return view('progress.index', compact('enrollments', 'stats', 'categories'));
    }
}