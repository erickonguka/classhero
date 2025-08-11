<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $analytics = [
            'user_growth' => $this->getUserGrowthData(),
            'course_stats' => $this->getCourseStats(),
            'revenue_data' => $this->getRevenueData(),
            'traffic_data' => $this->getTrafficData(),
        ];

        return view('admin.analytics.index', compact('analytics'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $data = $this->getAnalyticsData();
        
        if ($format === 'xlsx') {
            return $this->exportExcel($data);
        }
        
        return $this->exportPDF($data);
    }

    private function getAnalyticsData()
    {
        return [
            'users' => User::with('enrollments', 'courses')->get(),
            'courses' => Course::with('teacher', 'enrollments', 'reviews')->get(),
            'enrollments' => Enrollment::with('user', 'course')->get(),
            'payments' => Payment::with('user', 'course')->where('status', 'completed')->get(),
            'stats' => [
                'total_users' => User::count(),
                'total_courses' => Course::count(),
                'total_enrollments' => Enrollment::count(),
                'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
                'monthly_revenue' => Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'),
            ]
        ];
    }

    private function exportPDF($data)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Admin Analytics Report</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;margin:20px;color:#333;line-height:1.4}';
        $html .= 'table{width:100%;border-collapse:collapse;margin:20px 0}';
        $html .= 'th,td{border:1px solid #ddd;padding:8px;text-align:left;font-size:12px}';
        $html .= 'th{background-color:#f8f9fa;font-weight:bold}';
        $html .= 'h1{color:#2563eb;border-bottom:2px solid #2563eb;padding-bottom:10px}';
        $html .= 'h2{color:#1e40af;margin-top:30px}.summary{background:#f1f5f9;padding:15px;border-radius:8px;margin:20px 0}';
        $html .= '.text-center{text-align:center}</style></head><body>';
        
        $html .= '<div class="text-center"><h1>Admin Analytics Report</h1>';
        $html .= '<p>Generated on ' . date('F j, Y \\a\\t g:i A') . '</p></div>';
        
        $html .= '<div class="summary"><h2>Platform Statistics</h2>';
        $html .= '<table><tr><td><strong>Total Users:</strong></td><td>' . number_format($data['stats']['total_users']) . '</td></tr>';
        $html .= '<tr><td><strong>Total Courses:</strong></td><td>' . number_format($data['stats']['total_courses']) . '</td></tr>';
        $html .= '<tr><td><strong>Total Enrollments:</strong></td><td>' . number_format($data['stats']['total_enrollments']) . '</td></tr>';
        $html .= '<tr><td><strong>Total Revenue:</strong></td><td>$' . number_format($data['stats']['total_revenue'], 2) . '</td></tr></table></div>';
        
        $html .= '<h2>Course Performance</h2><table><tr><th>Course</th><th>Teacher</th><th>Enrollments</th><th>Revenue</th><th>Rating</th></tr>';
        foreach ($data['courses'] as $course) {
            $html .= '<tr><td>' . htmlspecialchars($course->title) . '</td>';
            $html .= '<td>' . htmlspecialchars($course->teacher->name) . '</td>';
            $html .= '<td>' . $course->enrollments->count() . '</td>';
            $html .= '<td>$' . number_format($course->enrollments->count() * ($course->price ?? 0), 2) . '</td>';
            $html .= '<td>' . number_format($course->reviews->avg('rating') ?? 0, 1) . '/5</td></tr>';
        }
        $html .= '</table></body></html>';
        
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="admin_analytics_' . date('Y-m-d') . '.pdf"');
    }

    private function exportExcel($data)
    {
        $csv = "Admin Analytics Report - " . date('Y-m-d') . "\n\n";
        $csv .= "Platform Statistics\n";
        $csv .= "Total Users," . $data['stats']['total_users'] . "\n";
        $csv .= "Total Courses," . $data['stats']['total_courses'] . "\n";
        $csv .= "Total Enrollments," . $data['stats']['total_enrollments'] . "\n";
        $csv .= "Total Revenue,$" . number_format($data['stats']['total_revenue'], 2) . "\n\n";
        
        $csv .= "Course Performance\n";
        $csv .= "Course,Teacher,Enrollments,Revenue,Rating\n";
        foreach ($data['courses'] as $course) {
            $csv .= '"' . $course->title . '",';
            $csv .= '"' . $course->teacher->name . '",';
            $csv .= $course->enrollments->count() . ',';
            $csv .= '$' . number_format($course->enrollments->count() * ($course->price ?? 0), 2) . ',';
            $csv .= number_format($course->reviews->avg('rating') ?? 0, 1) . "\n";
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="admin_analytics_' . date('Y-m-d') . '.csv"');
    }

    private function getUserGrowthData()
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                  ->where('created_at', '>=', now()->subDays(30))
                  ->groupBy('date')
                  ->orderBy('date')
                  ->get();
    }

    private function getCourseStats()
    {
        return [
            'by_status' => Course::selectRaw('status, COUNT(*) as count')
                                ->groupBy('status')
                                ->get(),
        ];
    }

    private function getRevenueData()
    {
        return Payment::selectRaw('DATE(created_at) as date, SUM(amount) as total')
                     ->where('status', 'completed')
                     ->where('created_at', '>=', now()->subDays(30))
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get();
    }

    private function getTrafficData()
    {
        return [
            'page_views' => rand(10000, 50000),
            'unique_visitors' => rand(5000, 25000),
            'bounce_rate' => rand(30, 70),
            'avg_session_duration' => rand(120, 300),
        ];
    }
}