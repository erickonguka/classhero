<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function checkout(Course $course)
    {
        if ($course->is_free) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'This course is free.');
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'You are already enrolled in this course.');
        }

        return view('payment.checkout', compact('course'));
    }

    public function process(Request $request, Course $course)
    {
        $request->validate([
            'payment_method' => 'required|in:card,paypal',
            'card_number' => 'required_if:payment_method,card',
            'card_expiry' => 'required_if:payment_method,card',
            'card_cvc' => 'required_if:payment_method,card',
            'card_name' => 'required_if:payment_method,card',
        ]);

        // Simulate payment processing
        $paymentId = 'pay_' . Str::random(20);
        
        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'payment_id' => $paymentId,
            'amount' => $course->price,
            'currency' => 'USD',
            'status' => 'completed', // In real app, this would be 'pending' initially
            'payment_method' => $request->payment_method,
            'payment_data' => [
                'card_last_four' => $request->payment_method === 'card' ? substr($request->card_number, -4) : null,
            ],
            'paid_at' => now(),
        ]);

        // Create enrollment
        Enrollment::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'total_lessons' => $course->lessons()->where('is_published', true)->count(),
        ]);

        // Update course enrolled count
        $course->increment('enrolled_count');

        return redirect()->route('payment.success', $payment)
            ->with('success', 'Payment successful! You are now enrolled in the course.');
    }

    public function success(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payment.success', compact('payment'));
    }

    public function webhook(Request $request)
    {
        // Handle payment webhooks from payment providers
        // This would contain real webhook handling logic
        
        $paymentId = $request->input('payment_id');
        $status = $request->input('status');

        $payment = Payment::where('payment_id', $paymentId)->first();
        
        if ($payment) {
            $payment->update(['status' => $status]);
            
            if ($status === 'completed' && !$payment->enrollment) {
                // Create enrollment if payment is completed
                Enrollment::create([
                    'user_id' => $payment->user_id,
                    'course_id' => $payment->course_id,
                    'enrolled_at' => now(),
                    'total_lessons' => $payment->course->lessons()->where('is_published', true)->count(),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}