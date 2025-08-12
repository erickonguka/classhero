<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\DiscussionController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

// Certificate verification (public)
Route::get('/certificate/verify/{code}', [CertificateController::class, 'verify'])->name('certificate.verify');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Learner Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Course enrollment and lessons
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])->name('lessons.show')->middleware('check.lesson.access');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'complete'])->name('lessons.complete');

    // Quiz routes
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');

    // Payment routes
    Route::get('/payment/{course}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/{course}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/{payment}/success', [PaymentController::class, 'success'])->name('payment.success');

    // Certificate routes
    Route::post('/certificate/{course}/generate', [CertificateController::class, 'generate'])->name('certificate.generate');
    Route::get('/certificate/{certificate}', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificate/{certificate}/download', [CertificateController::class, 'download'])->name('certificate.download');
    
    // Discussion routes
    Route::post('/discussions/{lesson}', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('/discussions/{lesson}', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/discussions/{discussion}/resolve', [DiscussionController::class, 'resolve'])->name('discussions.resolve');
    Route::get('/lessons/{lesson}/comment-count', [DiscussionController::class, 'commentCount'])->name('discussions.count');
    
    // Review routes
    Route::post('/courses/{course}/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('courses.review');
    
    // Discussion routes
    Route::get('/lessons/{lesson}/discussions', [App\Http\Controllers\DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/lessons/{lesson}/discussions', [App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('/lessons/{lesson}/comment-count', [App\Http\Controllers\DiscussionController::class, 'commentCount'])->name('discussions.count');
    
    // Teacher discussion moderation
    Route::post('/discussions/{discussion}/moderate', [App\Http\Controllers\Teacher\DiscussionController::class, 'moderate'])->name('discussions.moderate');
    Route::post('/discussions/{discussion}/approve', [App\Http\Controllers\Teacher\DiscussionController::class, 'approve'])->name('discussions.approve');
    
    // Video tracking
    Route::post('/lessons/{lesson}/video-progress', [App\Http\Controllers\VideoTrackingController::class, 'updateProgress'])->name('lessons.video-progress');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Progress routes
    Route::get('/progress', [App\Http\Controllers\ProgressController::class, 'index'])->name('progress.index');
    
    // My Courses routes
    Route::get('/my-courses', [App\Http\Controllers\MyCourseController::class, 'index'])->name('my-courses.index');
    
    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Teacher routes
Route::middleware(['auth', 'verified', 'mfa.verified'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', function () {
        return view('teacher.dashboard');
    })->name('dashboard');
    
    Route::resource('courses', TeacherCourseController::class);
    Route::resource('courses.lessons', App\Http\Controllers\Teacher\LessonController::class)->shallow();
    
    // Course lessons management
    Route::get('/courses/{course}/lessons', [App\Http\Controllers\Teacher\LessonController::class, 'index'])->name('courses.lessons.index');
    
    // Quiz routes for lessons
    Route::get('/lessons/{lesson}/quiz/create', [App\Http\Controllers\Teacher\LessonController::class, 'createQuiz'])->name('lessons.quiz.create');
    Route::post('/lessons/{lesson}/quiz', [App\Http\Controllers\Teacher\LessonController::class, 'storeQuiz'])->name('lessons.quiz.store');
    
    // Student management
    Route::get('/students', [App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('students.index');
    Route::get('/courses/{course}/students', [App\Http\Controllers\Teacher\StudentController::class, 'show'])->name('courses.students');
    Route::post('/enrollments/{enrollment}/ban', [App\Http\Controllers\Teacher\StudentController::class, 'ban'])->name('enrollments.ban');
    Route::post('/enrollments/{enrollment}/unban', [App\Http\Controllers\Teacher\StudentController::class, 'unban'])->name('enrollments.unban');
    
    // Comment management
    Route::get('/courses/{course}/comments', [TeacherCourseController::class, 'comments'])->name('courses.comments');
    Route::post('/discussions/{discussion}/approve', [TeacherCourseController::class, 'approveComment'])->name('discussions.approve');
    Route::post('/discussions/{discussion}/reject', [TeacherCourseController::class, 'rejectComment'])->name('discussions.reject');
    
    // Analytics - use teacher view directly
    Route::get('/analytics', function() {
        $teacher = auth()->user();
        $courses = $teacher->courses()->with(['enrollments.user', 'reviews'])->get();
        
        $totalCourses = $courses->count();
        $totalStudents = $courses->sum('enrolled_count');
        $totalRevenue = $courses->sum(function($course) {
            return $course->enrolled_count * ($course->price ?? 0);
        });
        
        $courseStats = $courses->map(function($course) {
            return [
                'title' => $course->title,
                'enrollments' => $course->enrolled_count,
                'lessons' => $course->lessons()->count(),
                'rating' => $course->reviews->avg('rating') ?? 0,
                'revenue' => $course->enrolled_count * ($course->price ?? 0)
            ];
        });
        
        // Get actual monthly enrollment data for current year
        $enrollmentData = array_fill(0, 12, 0);
        $monthlyEnrollments = \App\Models\Enrollment::whereIn('course_id', $courses->pluck('id'))
            ->whereYear('created_at', date('Y'))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        foreach ($monthlyEnrollments as $month => $count) {
            $enrollmentData[$month - 1] = $count;
        }
        
        $recentEnrollments = \App\Models\Enrollment::whereIn('course_id', $courses->pluck('id'))
            ->with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('teacher.analytics.index', compact(
            'totalCourses', 'totalStudents', 'totalRevenue', 
            'courseStats', 'enrollmentData', 'recentEnrollments'
        ));
    })->name('analytics');
    
    // Certification management
    Route::get('/certifications', [App\Http\Controllers\Teacher\CertificationController::class, 'index'])->name('certifications.index');
    Route::post('/certifications/{enrollment}/approve', [App\Http\Controllers\Teacher\CertificationController::class, 'approve'])->name('certifications.approve');
    Route::post('/certifications/{enrollment}/reject', [App\Http\Controllers\Teacher\CertificationController::class, 'reject'])->name('certifications.reject');
    Route::get('/certificates/{certificate}', [App\Http\Controllers\Teacher\CertificationController::class, 'viewCertificate'])->name('certificates.view');
    
    // Quiz management
    Route::resource('quizzes', App\Http\Controllers\Teacher\QuizController::class)->except(['create', 'store']);
    
    // Payment management
    Route::get('/payments', [App\Http\Controllers\Teacher\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/statement', [App\Http\Controllers\Teacher\PaymentController::class, 'downloadStatement'])->name('payments.statement');
    
    // Lesson comments
    Route::get('/lessons/{lesson}/comments', [App\Http\Controllers\Teacher\LessonController::class, 'comments'])->name('lessons.comments');
    Route::post('/discussions/{discussion}/reply', [App\Http\Controllers\Teacher\DiscussionController::class, 'reply'])->name('discussions.reply');
    Route::post('/discussions/{discussion}/resolve', [App\Http\Controllers\Teacher\DiscussionController::class, 'resolve'])->name('discussions.resolve');
    
    // Course publishing and management
    Route::post('/courses/{course}/publish', [TeacherCourseController::class, 'publish'])->name('courses.publish');
    Route::patch('/courses/{course}/publish', [TeacherCourseController::class, 'publish'])->name('courses.publish');
    Route::get('/courses/{course}/students', [TeacherCourseController::class, 'students'])->name('courses.students');
    Route::get('/courses/{course}/payments', [TeacherCourseController::class, 'payments'])->name('courses.payments');
});

// Admin routes
Route::middleware(['auth', 'verified', 'mfa.verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    
    // User management
    Route::get('/users/search', [App\Http\Controllers\Admin\UserController::class, 'search'])->name('users.search');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/ban', [App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [App\Http\Controllers\Admin\UserController::class, 'unban'])->name('users.unban');
    Route::get('/users/{user}/login-as', [App\Http\Controllers\Admin\UserController::class, 'loginAs'])->name('users.login-as');
    Route::post('/users/batch-action', [App\Http\Controllers\Admin\UserController::class, 'batchAction'])->name('users.batch-action');
    
    // Category management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    
    // Course management
    Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);
    Route::post('/courses/{course}/approve', [App\Http\Controllers\Admin\CourseController::class, 'approve'])->name('courses.approve');
    Route::post('/courses/{course}/reject', [App\Http\Controllers\Admin\CourseController::class, 'reject'])->name('courses.reject');
    Route::post('/courses/{course}/status', [App\Http\Controllers\Admin\CourseController::class, 'updateStatus'])->name('courses.status');
    Route::post('/courses/batch-action', [App\Http\Controllers\Admin\CourseController::class, 'batchAction'])->name('courses.batch-action');
    
    // Notification management
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    
    // Storage management
    Route::get('/storage', [App\Http\Controllers\Admin\StorageController::class, 'index'])->name('storage');
    Route::post('/storage/cleanup', [App\Http\Controllers\Admin\StorageController::class, 'cleanup'])->name('storage.cleanup');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/backup', [App\Http\Controllers\Admin\SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    
    // MFA Setup
    Route::get('/mfa-setup', function() {
        return view('admin.mfa-setup');
    })->name('mfa-setup');
    Route::post('/mfa/regenerate-codes', [App\Http\Controllers\Auth\MfaController::class, 'regenerateRecoveryCodes'])->name('mfa.regenerate-codes');
    Route::post('/mfa/disable', [App\Http\Controllers\Auth\MfaController::class, 'disableMfa'])->name('mfa.disable');
    

    
    // Certificate management
    Route::get('/certificates', [App\Http\Controllers\Admin\UserController::class, 'certificates'])->name('certificates.index');
    Route::get('/certificates/{certificate}/view', [App\Http\Controllers\Admin\UserController::class, 'viewCertificate'])->name('certificates.view');
    Route::get('/certificates/{certificate}/download', [App\Http\Controllers\Admin\UserController::class, 'downloadCertificate'])->name('certificates.download');
});

// CSRF token refresh route
Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});

// Test CSRF endpoint
Route::post('/test-csrf-endpoint', function(\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'CSRF token is valid!',
        'data' => $request->all()
    ]);
});

// CSRF test page
Route::get('/test-csrf', function() {
    return view('test-csrf');
});

// Webhook routes (no auth required)
Route::post('/webhook/payment', [PaymentController::class, 'webhook'])->name('payment.webhook');

// MFA routes
Route::middleware(['auth'])->group(function () {
    Route::get('/mfa', [App\Http\Controllers\Auth\MfaController::class, 'showMfaForm'])->name('mfa.show');
    Route::post('/mfa/verify', [App\Http\Controllers\Auth\MfaController::class, 'verifyAuthenticator'])->name('mfa.verify.authenticator');
});

require __DIR__.'/auth.php';
require __DIR__.'/currency.php';