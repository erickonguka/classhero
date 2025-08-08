<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // Redirect to the relevant page based on notification type
        $data = json_decode($notification->data, true);
        
        switch ($notification->type) {
            case 'certificate_approved':
                return redirect()->route('certificate.show', $data['certificate_id']);
            case 'reply':
            case 'comment':
                $course = \App\Models\Course::find($data['course_id']);
                $lesson = \App\Models\Lesson::find($data['lesson_id']);
                if ($course && $lesson) {
                    return redirect()->route('lessons.show', [$course->slug, $lesson->slug]);
                }
                return redirect()->route('progress.index');
            case 'course_completion':
                return redirect()->route('teacher.certifications.index');
            default:
                return redirect()->route('dashboard');
        }
    }
    
    public function markAllAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }
}