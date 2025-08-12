<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificationController extends Controller
{
    public function index(Request $request)
    {
        $pendingQuery = Enrollment::whereHas('course', function($query) {
            $query->where('teacher_id', Auth::id());
        })
        ->where('progress_percentage', 100)
        ->whereNull('completed_at')
        ->whereNull('rejected_at')
        ->with(['user', 'course']);

        $rejectedQuery = Enrollment::whereHas('course', function($query) {
            $query->where('teacher_id', Auth::id());
        })
        ->whereNotNull('rejected_at')
        ->with(['user', 'course']);

        $approvedQuery = Enrollment::whereHas('course', function($query) {
            $query->where('teacher_id', Auth::id());
        })
        ->whereNotNull('completed_at')
        ->with(['user', 'course', 'certificateRelation']);

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $pendingQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', $search)->orWhere('email', 'like', $search);
            });
            $rejectedQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', $search)->orWhere('email', 'like', $search);
            });
            $approvedQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', $search)->orWhere('email', 'like', $search);
            });
        }

        if ($request->course_id) {
            $pendingQuery->whereHas('course', function($q) use ($request) {
                $q->where('id', $request->course_id);
            });
            $rejectedQuery->whereHas('course', function($q) use ($request) {
                $q->where('id', $request->course_id);
            });
            $approvedQuery->whereHas('course', function($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        $completedEnrollments = $pendingQuery->orderBy($sort, $direction)->paginate(15, ['*'], 'pending');
        $rejectedEnrollments = $rejectedQuery->orderBy('rejected_at', 'desc')->paginate(10, ['*'], 'rejected');
        $approvedEnrollments = $approvedQuery->orderBy('completed_at', 'desc')->paginate(10, ['*'], 'approved');
        
        $courses = Auth::user()->courses;

        return view('teacher.certifications.index', compact('completedEnrollments', 'rejectedEnrollments', 'approvedEnrollments', 'courses'));
    }

    public function approve(Enrollment $enrollment)
    {
        if ($enrollment->course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if learner is qualified for certification
        if ($enrollment->progress_percentage < 100) {
            return response()->json(['error' => 'Student has not completed the course (Progress: ' . $enrollment->progress_percentage . '%)'], 400);
        }

        // Check if all required quizzes are passed
        $course = $enrollment->course;
        $failedQuizzes = [];
        
        foreach ($course->lessons as $lesson) {
            if ($lesson->quiz) {
                $bestAttempt = $lesson->quiz->attempts()
                    ->where('user_id', $enrollment->user_id)
                    ->orderBy('score', 'desc')
                    ->first();
                    
                if (!$bestAttempt || $bestAttempt->score < $lesson->quiz->passing_score) {
                    $failedQuizzes[] = $lesson->title;
                }
            }
        }
        
        if (!empty($failedQuizzes)) {
            return response()->json(['error' => 'Student has not passed required quizzes: ' . implode(', ', $failedQuizzes)], 400);
        }

        $enrollment->update(['completed_at' => now()]);

        // Generate certificate
        $certificate = Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_number' => 'CERT-' . strtoupper(uniqid()),
            'verification_code' => 'VER-' . strtoupper(uniqid()),
            'issued_at' => now(),
        ]);

        // Notify learner about certificate approval
        $enrollment->user->notify(new \App\Notifications\SystemNotification([
            'title' => 'Certificate Approved!',
            'message' => 'Congratulations! Your certificate for "' . $enrollment->course->title . '" has been approved and is now available for download.',
            'type' => 'certificate_approved',
            'course_id' => $enrollment->course_id,
            'certificate_id' => $certificate->id,
            'course_title' => $enrollment->course->title
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Certificate approved and generated successfully!'
        ]);
    }

    public function reject(Enrollment $enrollment, Request $request)
    {
        if ($enrollment->course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        // Reset progress to require re-completion
        $enrollment->update([
            'progress_percentage' => 95, // Just below completion
            'rejection_reason' => $request->reason,
            'rejected_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certification rejected. Student will need to complete requirements again.'
        ]);
    }
    
    public function viewCertificate(Certificate $certificate)
    {
        // Check if teacher owns the course for this certificate
        if ($certificate->course->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to certificate');
        }
        
        return view('certificate.show', compact('certificate'));
    }
}