<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function moderate(Discussion $discussion)
    {
        // Check if teacher owns the course
        if (Auth::id() !== $discussion->lesson->course->teacher_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $discussion->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Comment hidden']);
    }

    public function approve(Discussion $discussion)
    {
        // Check if teacher owns the course
        if (Auth::id() !== $discussion->lesson->course->teacher_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $discussion->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'Comment approved']);
    }
}