<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Course::where('teacher_id', Auth::id())
            ->with(['category', 'lessons']);

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $courses = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('teacher.courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('teacher.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_free' => 'required|boolean',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'what_you_learn' => 'nullable|array',
            'what_you_learn.*' => 'string|max:500',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:500',
            'tags_input' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
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
            'is_free' => true, // All courses are free for now
            'price' => null,
            'duration_hours' => $request->duration_hours,
            'what_you_learn' => array_filter($request->what_you_learn ?? [], fn($item) => !empty(trim($item))),
            'requirements' => array_filter($request->requirements ?? [], fn($item) => !empty(trim($item))),
            'tags' => $request->tags_input ? array_filter(array_map('trim', explode(',', $request->tags_input)), fn($item) => !empty($item)) : [],
        ]);

        if ($request->hasFile('thumbnail')) {
            $course->addMediaFromRequest('thumbnail')
                ->toMediaCollection('thumbnails');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Course created successfully! Now add some lessons to help students learn.',
                'course' => $course->load('category'),
                'redirect_url' => route('teacher.courses.lessons.create', $course),
                'suggest_lessons' => true
            ], 201);
        }

        return redirect()->route('teacher.courses.lessons.create', $course)
            ->with('success', 'Course created successfully! Now add some lessons to help students learn.');
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_free' => 'required|boolean',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'what_you_learn' => 'nullable|array',
            'what_you_learn.*' => 'string|max:500',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:500',
            'tags_input' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $course->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'difficulty' => $request->difficulty,
            'is_free' => true, // All courses are free for now
            'price' => null,
            'duration_hours' => $request->duration_hours,
            'what_you_learn' => array_filter($request->what_you_learn ?? [], fn($item) => !empty(trim($item))),
            'requirements' => array_filter($request->requirements ?? [], fn($item) => !empty(trim($item))),
            'tags' => $request->tags_input ? array_filter(array_map('trim', explode(',', $request->tags_input)), fn($item) => !empty($item)) : [],
        ]);

        if ($request->hasFile('thumbnail')) {
            $course->clearMediaCollection('thumbnails');
            $course->addMediaFromRequest('thumbnail')
                ->toMediaCollection('thumbnails');
        }

        // Recalculate progress for all enrolled students when course is updated
        $course->enrollments()->chunk(100, function($enrollments) {
            foreach ($enrollments as $enrollment) {
                $enrollment->recalculateProgress();
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Course updated successfully!',
                'course' => $course->fresh()->load('category'),
                'redirect_url' => route('teacher.courses.show', $course),
            ], 200);
        }

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Course updated successfully! Student progress has been recalculated.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $course->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully!'
            ]);
        }

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

    public function publish(Course $course)
    {
        $this->authorize('update', $course);
        
        if (!$course->canBePublished()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Course must have at least 1 lesson to be published.'], 400);
            }
            return back()->with('error', 'Course must have at least 1 lesson to be published.');
        }
        
        $course->update(['status' => 'pending']);
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Course submitted for admin approval!']);
        }
        
        return back()->with('success', 'Course submitted for admin approval!');
    }

    public function students(Course $course)
    {
        $this->authorize('view', $course);
        
        $enrollments = $course->enrollments()
            ->with(['user', 'lessonProgress'])
            ->latest()
            ->paginate(20);
            
        return view('teacher.courses.students', compact('course', 'enrollments'));
    }

    public function payments(Course $course)
    {
        $this->authorize('view', $course);
        
        $payments = $course->payments()
            ->with('user')
            ->where('status', 'completed')
            ->latest()
            ->paginate(20);
            
        $totalRevenue = $payments->sum('amount') * 0.7; // 70% to teacher
        
        return view('teacher.courses.payments', compact('course', 'payments', 'totalRevenue'));
    }
}