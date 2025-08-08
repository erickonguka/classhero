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
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Teacher routes
Route::middleware(['auth', 'verified'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', function () {
        return view('teacher.dashboard');
    })->name('dashboard');
    
    Route::resource('courses', TeacherCourseController::class);
    Route::resource('courses.lessons', App\Http\Controllers\Teacher\LessonController::class)->shallow();
    Route::resource('lessons', App\Http\Controllers\Teacher\LessonController::class)->only(['show', 'edit', 'update', 'destroy']);
    
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
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Teacher\AnalyticsController::class, 'index'])->name('analytics');
    
    // Certification management
    Route::get('/certifications', [App\Http\Controllers\Teacher\CertificationController::class, 'index'])->name('certifications.index');
    Route::post('/certifications/{enrollment}/approve', [App\Http\Controllers\Teacher\CertificationController::class, 'approve'])->name('certifications.approve');
    Route::post('/certifications/{enrollment}/reject', [App\Http\Controllers\Teacher\CertificationController::class, 'reject'])->name('certifications.reject');
    
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
    Route::get('/courses/{course}/students', [TeacherCourseController::class, 'students'])->name('courses.students');
    Route::get('/courses/{course}/payments', [TeacherCourseController::class, 'payments'])->name('courses.payments');
});

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    
    // User management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/ban', [App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [App\Http\Controllers\Admin\UserController::class, 'unban'])->name('users.unban');
    
    // Category management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    
    // Course management
    Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);
    Route::post('/courses/{course}/approve', [App\Http\Controllers\Admin\CourseController::class, 'approve'])->name('courses.approve');
    Route::post('/courses/{course}/reject', [App\Http\Controllers\Admin\CourseController::class, 'reject'])->name('courses.reject');
});

// Webhook routes (no auth required)
Route::post('/webhook/payment', [PaymentController::class, 'webhook'])->name('payment.webhook');

require __DIR__.'/auth.php';
require __DIR__.'/currency.php';