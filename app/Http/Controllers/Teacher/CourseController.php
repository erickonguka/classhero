<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $courses = Course::where('teacher_id', Auth::id())
            ->with(['category', 'lessons'])
            ->latest()
            ->paginate(10);

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('teacher.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_free' => 'required|boolean',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'what_you_learn' => 'nullable|array',
            'requirements' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'overview' => $request->overview,
            'instructor_info' => $request->instructor_info,
            'teacher_id' => Auth::id(),
            'category_id' => $request->category_id,
            'difficulty' => $request->difficulty,
            'status' => 'draft',
            'is_free' => $request->is_free,
            'price' => $request->is_free ? null : $request->price,
            'duration_hours' => $request->duration_hours,
            'what_you_learn' => $request->what_you_learn ?? [],
            'requirements' => $request->requirements ?? [],
        ]);

        if ($request->hasFile('thumbnail')) {
            $course->addMediaFromRequest('thumbnail')
                   ->toMediaCollection('thumbnails');
        }

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        
        $course->load(['lessons' => function($query) {
            $query->orderBy('order')->with(['discussions' => function($q) {
                $q->where('status', 'pending');
            }]);
        }, 'category']);

        $analytics = [
            'total_enrollments' => $course->enrolled_count,
            'total_lessons' => $course->lessons->count(),
            'completion_rate' => $course->enrollments()->where('progress_percentage', 100)->count(),
            'average_rating' => $course->rating,
        ];

        return view('teacher.courses.show', compact('course', 'analytics'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        $categories = Category::where('is_active', true)->get();
        return view('teacher.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_free' => 'required|boolean',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'what_you_learn' => 'nullable|array',
            'requirements' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $course->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'difficulty' => $request->difficulty,
            'is_free' => $request->is_free,
            'price' => $request->is_free ? null : $request->price,
            'duration_hours' => $request->duration_hours,
            'what_you_learn' => $request->what_you_learn ?? [],
            'requirements' => $request->requirements ?? [],
        ]);

        if ($request->hasFile('thumbnail')) {
            $course->clearMediaCollection('thumbnails');
            $course->addMediaFromRequest('thumbnail')
                   ->toMediaCollection('thumbnails');
        }

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    public function comments(Course $course)
    {
        $this->authorize('view', $course);
        
        $pendingComments = Discussion::whereHas('lesson', function($query) use ($course) {
            $query->where('course_id', $course->id);
        })->where('status', 'pending')
          ->with(['user', 'lesson'])
          ->latest()
          ->paginate(10);

        return view('teacher.courses.comments', compact('course', 'pendingComments'));
    }

    public function approveComment(Discussion $discussion)
    {
        // Check if teacher owns the course
        if (Auth::id() !== $discussion->lesson->course->teacher_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $discussion->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'Comment approved']);
    }

    public function rejectComment(Discussion $discussion)
    {
        // Check if teacher owns the course
        if (Auth::id() !== $discussion->lesson->course->teacher_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $discussion->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Comment rejected']);
    }
}