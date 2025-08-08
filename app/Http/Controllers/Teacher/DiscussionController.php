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
            'media' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
        ]);

        $reply = Discussion::create([
            'user_id' => Auth::id(),
            'lesson_id' => $discussion->lesson_id,
            'content' => $request->content,
            'parent_id' => $discussion->id,
        ]);

        if ($request->hasFile('media')) {
            $reply->addMediaFromRequest('media')
                  ->toMediaCollection('attachments');
        }

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