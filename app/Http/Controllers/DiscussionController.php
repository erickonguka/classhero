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
                $query->where('status', 'approved')
                    ->with(['user', 'replies' => function($subQuery) {
                        $subQuery->where('status', 'approved')->with('user')->orderBy('created_at', 'asc');
                    }])
                    ->orderBy('created_at', 'asc');
            }])
            ->latest()
            ->get()
            ->map(function($discussion) {
                $discussion->user->profile_picture_url = $discussion->user->getProfilePictureUrl();
                $discussion->user->is_course_author = $discussion->user->id === $discussion->lesson->course->teacher_id;
                $mediaUrl = $discussion->getFirstMediaUrl('attachments');
                $discussion->media_url = $mediaUrl ? url($mediaUrl) : null;
                if ($discussion->replies) {
                    $discussion->replies->each(function($reply) {
                        $reply->user->profile_picture_url = $reply->user->getProfilePictureUrl();
                        $reply->user->is_course_author = $reply->user->id === $reply->lesson->course->teacher_id;
                        $mediaUrl = $reply->getFirstMediaUrl('attachments');
                        $reply->media_url = $mediaUrl ? url($mediaUrl) : null;
                        if ($reply->replies) {
                            $reply->replies->each(function($subReply) {
                                $subReply->user->profile_picture_url = $subReply->user->getProfilePictureUrl();
                                $subReply->user->is_course_author = $subReply->user->id === $subReply->lesson->course->teacher_id;
                                $mediaUrl = $subReply->getFirstMediaUrl('attachments');
                                $subReply->media_url = $mediaUrl ? url($mediaUrl) : null;
                            });
                        }
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
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,docx,doc,mp3,wav,ogg,webm,mp4,avi,mov|max:10240',
        ]);

        // Check enrollment (course authors can always comment)
        if ($lesson->course->teacher_id !== Auth::id()) {
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

        // All users can upload media now

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

        // Create notification for course teacher (if not the teacher posting)
        if (Auth::id() !== $lesson->course->teacher_id) {
            $lesson->course->teacher->notify(new \App\Notifications\SystemNotification([
                'title' => 'New Comment on Your Course',
                'message' => Auth::user()->name . ' commented on "' . $lesson->title . '"',
                'type' => 'comment',
                'lesson_id' => $lesson->id,
                'course_id' => $lesson->course_id,
                'discussion_id' => $discussion->id,
                'user_name' => Auth::user()->name,
                'course_title' => $lesson->course->title
            ]));
        }

        // If replying to someone, notify the original commenter
        if ($request->parent_id) {
            $parentDiscussion = Discussion::find($request->parent_id);
            if ($parentDiscussion && $parentDiscussion->user_id !== Auth::id()) {
                $parentDiscussion->user->notify(new \App\Notifications\SystemNotification([
                    'title' => 'Reply to Your Comment',
                    'message' => Auth::user()->name . ' replied to your comment on "' . $lesson->title . '"',
                    'type' => 'reply',
                    'lesson_id' => $lesson->id,
                    'course_id' => $lesson->course_id,
                    'discussion_id' => $discussion->id,
                    'parent_discussion_id' => $request->parent_id,
                    'user_name' => Auth::user()->name
                ]));
            }
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
        // Only course author can resolve
        if (Auth::id() !== $discussion->lesson->course->teacher_id) {
            return response()->json(['error' => 'Only course authors can resolve discussions'], 403);
        }

        $discussion->update(['is_resolved' => !$discussion->is_resolved]);
        return response()->json([
            'success' => true, 
            'message' => $discussion->is_resolved ? 'Discussion marked as resolved' : 'Discussion reopened',
            'is_resolved' => $discussion->is_resolved
        ]);
    }
}