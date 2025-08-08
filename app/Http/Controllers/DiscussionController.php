<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Lesson;
use App\Http\Controllers\CommentModerationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function index(Lesson $lesson)
    {
        $discussions = Discussion::where('lesson_id', $lesson->id)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->with(['user', 'replies' => function($query) {
                $query->where('status', 'approved')->with('user');
            }])
            ->latest()
            ->get()
            ->map(function($discussion) {
                $discussion->user->profile_picture_url = $discussion->user->getProfilePictureUrl();
                if ($discussion->replies) {
                    $discussion->replies->each(function($reply) {
                        $reply->user->profile_picture_url = $reply->user->getProfilePictureUrl();
                    });
                }
                return $discussion;
            });

        return response()->json(['discussions' => $discussions]);
    }

    public function commentCount(Lesson $lesson)
    {
        $count = Discussion::where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->whereNull('parent_id')
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:discussions,id',
            'media' => 'nullable|file|max:10240',
        ]);

        // Check enrollment (teachers can always comment on their own courses)
        if (!Auth::user()->isTeacher() || $lesson->course->teacher_id !== Auth::id()) {
            $enrollment = Auth::user()->enrollments()->where('course_id', $lesson->course_id)->first();
            if (!$enrollment) {
                return response()->json(['error' => 'You must be enrolled to comment'], 403);
            }
        }

        // Check comment limit (5 per lesson per user) - teachers have no limit
        if (!Auth::user()->isTeacher()) {
            $userComments = Discussion::where('user_id', Auth::id())
                ->where('lesson_id', $lesson->id)
                ->whereNull('parent_id')
                ->count();
            
            if ($userComments >= 5 && !$request->parent_id) {
                return response()->json(['error' => 'You have reached the comment limit for this lesson'], 422);
            }
        }

        // Check banned words
        $bannedWords = ['spam', 'scam', 'fake', 'stupid', 'idiot', 'hate', 'kill', 'die', 'damn', 'hell', 'fuck', 'shit', 'bitch', 'asshole'];
        $content = strtolower($request->content);
        foreach ($bannedWords as $word) {
            if (strpos($content, $word) !== false) {
                return response()->json(['error' => 'Your comment contains inappropriate content and cannot be posted'], 422);
            }
        }

        // Only teachers can upload media
        if ($request->hasFile('media') && !Auth::user()->isTeacher()) {
            return response()->json(['error' => 'Only teachers can attach media to comments'], 403);
        }
        
        // Teachers can comment on any lesson in their courses
        if (Auth::user()->isTeacher() && $lesson->course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'You can only comment on your own course lessons'], 403);
        }

        $discussion = Discussion::create([
            'user_id' => Auth::id(),
            'lesson_id' => $lesson->id,
            'parent_id' => $request->parent_id ?: null,
            'content' => $request->content,
            'status' => 'approved',
            'has_media' => $request->hasFile('media'),
            'media_type' => $request->hasFile('media') ? $request->file('media')->getClientOriginalExtension() : null,
        ]);

        if ($request->hasFile('media')) {
            $discussion->addMediaFromRequest('media')->toMediaCollection('attachments');
        }

        // Mark comment requirement as completed
        $progress = $lesson->userProgress(Auth::id());
        if ($progress) {
            $progress->update(['comment_posted' => true]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Comment posted successfully',
            'comment' => $discussion->load('user')
        ]);
    }

    public function resolve(Discussion $discussion)
    {
        // Only lesson owner or admin can resolve
        if (Auth::id() !== $discussion->lesson->course->teacher_id && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $discussion->update(['is_resolved' => true]);
        return response()->json(['success' => true]);
    }
}