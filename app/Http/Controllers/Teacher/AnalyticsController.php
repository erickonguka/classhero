<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = Auth::id();
        
        // Basic stats
        $totalCourses = Course::where('teacher_id', $teacherId)->count();
        $totalStudents = Enrollment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
        
        $totalRevenue = Payment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('status', 'completed')->sum('amount');
        
        // Course performance
        $courseStats = Course::where('teacher_id', $teacherId)
            ->withCount(['enrollments', 'lessons'])
            ->with(['reviews'])
            ->get()
            ->map(function($course) {
                return [
                    'title' => $course->title,
                    'enrollments' => $course->enrollments_count,
                    'lessons' => $course->lessons_count,
                    'rating' => $course->reviews->avg('rating') ?? 0,
                    'revenue' => $course->is_free ? 0 : ($course->enrollments_count * $course->price * 0.7)
                ];
            });

        // Monthly enrollment trends
        $enrollmentData = [];
        for ($i = 1; $i <= 12; $i++) {
            $enrollmentData[] = Enrollment::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->whereMonth('created_at', $i)
            ->whereYear('created_at', now()->year)
            ->count();
        }

        // Recent activity with filtering and sorting
        $enrollmentsQuery = Enrollment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with(['user', 'course']);
        
        // Apply date filter
        if ($request->date_filter) {
            switch ($request->date_filter) {
                case 'today':
                    $enrollmentsQuery->whereDate('created_at', today());
                    break;
                case 'week':
                    $enrollmentsQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $enrollmentsQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $enrollmentsQuery->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        // Apply sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        if ($sort === 'name') {
            $enrollmentsQuery->join('users', 'enrollments.user_id', '=', 'users.id')
                           ->orderBy('users.name', $direction)
                           ->select('enrollments.*');
        } elseif ($sort === 'progress') {
            $enrollmentsQuery->orderBy('progress_percentage', $direction);
        } else {
            $enrollmentsQuery->orderBy('created_at', $direction);
        }
        
        $recentEnrollments = $enrollmentsQuery->take(10)->get();

        return view('teacher.analytics.index', compact(
            'totalCourses',
            'totalStudents', 
            'totalRevenue',
            'courseStats',
            'enrollmentData',
            'recentEnrollments'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $teacherId = Auth::id();
        
        $data = $this->getAnalyticsData($teacherId);
        
        if ($format === 'pdf') {
            return $this->exportPDF($data);
        }
        
        return $this->exportCSV($data);
    }
    
    private function getAnalyticsData($teacherId)
    {
        return [
            'courses' => Course::where('teacher_id', $teacherId)->withCount(['enrollments', 'lessons'])->with(['reviews'])->get(),
            'enrollments' => Enrollment::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->with(['user', 'course'])->get(),
            'payments' => Payment::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->where('status', 'completed')->with(['user', 'course'])->get()
        ];
    }
    
    private function exportCSV($data)
    {
        $csv = "Course Analytics Report\n\n";
        $csv .= "Course,Students,Lessons,Rating,Revenue\n";
        
        foreach ($data['courses'] as $course) {
            $csv .= sprintf(
                "%s,%d,%d,%.1f,$%.2f\n",
                $course->title,
                $course->enrollments_count,
                $course->lessons_count,
                $course->reviews->avg('rating') ?? 0,
                $course->is_free ? 0 : ($course->enrollments_count * ($course->price ?? 0) * 0.7)
            );
        }
        
        $csv .= "\n\nStudent Enrollments\n";
        $csv .= "Student,Email,Country,Course,Date\n";
        
        foreach ($data['enrollments'] as $enrollment) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $enrollment->user->name,
                $enrollment->user->email,
                $enrollment->user->getCountryName(),
                $enrollment->course->title,
                $enrollment->created_at->format('Y-m-d')
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="analytics_' . date('Y-m-d') . '.csv"');
    }
    
    private function exportPDF($data)
    {
        // Generate PDF-style HTML content
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Analytics Report</title><style>@page{size:A4;margin:1in}body{font-family:Arial,sans-serif;margin:0;padding:20px;color:#333;line-height:1.4}table{width:100%;border-collapse:collapse;margin:20px 0;page-break-inside:avoid}th,td{border:1px solid #ddd;padding:8px;text-align:left;font-size:12px}th{background-color:#f8f9fa;font-weight:bold}h1{color:#2563eb;border-bottom:2px solid #2563eb;padding-bottom:10px;font-size:24px}h2{color:#1e40af;margin-top:30px;font-size:18px}.summary{background:#f1f5f9;padding:15px;border-radius:8px;margin:20px 0;page-break-inside:avoid}.text-center{text-align:center}.header{border-bottom:3px solid #2563eb;padding-bottom:20px;margin-bottom:30px}.footer{margin-top:40px;padding-top:20px;border-top:1px solid #ddd;text-align:center;color:#666;font-size:10px}@media print{body{margin:0}}</style></head><body>';
        
        $html .= '<div class="header text-center"><h1>Teacher Analytics Report</h1><p style="font-size:14px;color:#666;">Generated on ' . date('F j, Y \a\t g:i A') . '</p></div>';
        
        // Summary stats
        $totalCourses = $data['courses']->count();
        $totalStudents = $data['enrollments']->count();
        $totalRevenue = $data['payments']->sum('amount');
        
        $html .= '<div class="summary"><h2 style="margin-top:0;">Executive Summary</h2>';
        $html .= '<table style="margin:10px 0;"><tr><td style="border:none;background:none;font-weight:bold;">Total Courses:</td><td style="border:none;background:none;">' . $totalCourses . '</td></tr>';
        $html .= '<tr><td style="border:none;background:none;font-weight:bold;">Total Students:</td><td style="border:none;background:none;">' . $totalStudents . '</td></tr>';
        $html .= '<tr><td style="border:none;background:none;font-weight:bold;">Total Revenue:</td><td style="border:none;background:none;">$' . number_format($totalRevenue, 2) . '</td></tr></table></div>';
        
        $html .= '<h2>Course Performance Analysis</h2>';
        $html .= '<table><thead><tr><th>Course Title</th><th>Enrolled Students</th><th>Total Lessons</th><th>Average Rating</th><th>Revenue Generated</th></tr></thead><tbody>';
        
        foreach ($data['courses'] as $course) {
            $html .= sprintf(
                '<tr><td>%s</td><td style="text-align:center;">%d</td><td style="text-align:center;">%d</td><td style="text-align:center;">%.1f/5.0</td><td style="text-align:right;">$%.2f</td></tr>',
                htmlspecialchars($course->title),
                $course->enrollments_count,
                $course->lessons_count,
                $course->reviews->avg('rating') ?? 0,
                $course->is_free ? 0 : ($course->enrollments_count * ($course->price ?? 0) * 0.7)
            );
        }
        
        $html .= '</tbody></table><h2 style="page-break-before:always;">Student Enrollment Details</h2>';
        $html .= '<table><thead><tr><th>Student Name</th><th>Email Address</th><th>Country</th><th>Course Enrolled</th><th>Enrollment Date</th></tr></thead><tbody>';
        
        foreach ($data['enrollments'] as $enrollment) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s %s</td><td>%s</td><td style="text-align:center;">%s</td></tr>',
                htmlspecialchars($enrollment->user->name),
                htmlspecialchars($enrollment->user->email),
                $enrollment->user->getCountryFlag(),
                htmlspecialchars($enrollment->user->getCountryName()),
                htmlspecialchars($enrollment->course->title),
                $enrollment->created_at->format('M j, Y')
            );
        }
        
        $html .= '</tbody></table><div class="footer"><p>This report was generated by ClassHero Analytics System<br>© ' . date('Y') . ' ClassHero. All rights reserved.</p></div></body></html>';
        
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="analytics_report_' . date('Y-m-d') . '.pdf"');
    }
}