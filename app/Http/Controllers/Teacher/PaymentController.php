<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::whereHas('course', function($q) {
            $q->where('teacher_id', Auth::id());
        })->with(['user', 'course'])->where('status', 'completed');

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->date_filter) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        if ($request->month && $request->year) {
            $query->whereMonth('created_at', $request->month)->whereYear('created_at', $request->year);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $payments = $query->paginate(20);
        $totalRevenue = $payments->sum('amount') * 0.7;
        $courses = Auth::user()->courses;

        return view('teacher.payments.index', compact('payments', 'totalRevenue', 'courses'));
    }

    public function downloadStatement(Request $request)
    {
        $payments = Payment::whereHas('course', function($q) {
            $q->where('teacher_id', Auth::id());
        })->with(['user', 'course'])->where('status', 'completed');

        if ($request->month && $request->year) {
            $payments->whereMonth('created_at', $request->month)->whereYear('created_at', $request->year);
        }

        $payments = $payments->orderBy('created_at', 'desc')->get();
        $totalRevenue = $payments->sum('amount') * 0.7;

        $csv = "Date,Student,Course,Amount,Your Share,Payment Method\n";
        foreach ($payments as $payment) {
            $csv .= sprintf(
                "%s,%s,%s,$%s,$%s,%s\n",
                $payment->created_at->format('Y-m-d H:i'),
                $payment->user->name,
                $payment->course->title,
                number_format($payment->amount, 2),
                number_format($payment->amount * 0.7, 2),
                $payment->payment_method
            );
        }

        $filename = 'payment_statement_' . ($request->month ? $request->month . '_' . $request->year : 'all') . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}