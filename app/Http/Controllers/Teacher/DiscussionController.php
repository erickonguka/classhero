<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function reply(Request $request, Discussion $discussion)
    {
        if ($discussion->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $discussion->replies()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Reply posted successfully!');
    }

    public function resolve(Discussion $discussion)
    {
        if ($discussion->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $discussion->update(['is_resolved' => true]);

        return redirect()->back()->with('success', 'Discussion marked as resolved!');
    }
}