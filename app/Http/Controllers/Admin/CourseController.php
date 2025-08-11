<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['teacher', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('teacher', function($tq) use ($request) {
                      $tq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $courses = $query->latest()->paginate(20);
        
        return view('admin.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'category', 'lessons', 'enrollments.user']);
        return view('admin.courses.show', compact('course'));
    }

    public function approve(Course $course)
    {
        $course->update(['status' => 'published']);
        return back()->with('success', 'Course approved and published successfully!');
    }

    public function reject(Request $request, Course $course)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        
        $course->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);
        
        return back()->with('success', 'Course rejected with feedback sent to teacher.');
    }

    public function updateStatus(Request $request, Course $course)
    {
        $request->validate([
            'status' => 'required|in:draft,pending,published,rejected'
        ]);
        
        $course->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Course status updated successfully!'
        ]);
    }
    
    public function batchAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,draft,pending,reject,delete',
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id'
        ]);
        
        $courses = Course::whereIn('id', $request->courses)->get();
        $count = 0;
        
        foreach ($courses as $course) {
            switch ($request->action) {
                case 'publish':
                    $course->update(['status' => 'published']);
                    $count++;
                    break;
                case 'draft':
                    $course->update(['status' => 'draft']);
                    $count++;
                    break;
                case 'pending':
                    $course->update(['status' => 'pending']);
                    $count++;
                    break;
                case 'reject':
                    $course->update(['status' => 'rejected']);
                    $count++;
                    break;
                case 'delete':
                    $course->delete();
                    $count++;
                    break;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$count} courses"
        ]);
    }
    
    public function destroy(Course $course)
    {
        $course->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully!'
            ]);
        }
        
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
    }
}