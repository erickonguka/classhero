<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminAnnouncement;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:announcement,alert,info',
            'recipients' => 'required|in:all,teachers,learners,admins,new_users,specific',
            'user_id' => 'required_if:recipients,specific|nullable|exists:users,id',
            'send_email' => 'boolean'
        ]);
        
        // Clear user_id if not specific recipient
        if ($validated['recipients'] !== 'specific') {
            $validated['user_id'] = null;
        }

        $usersQuery = $this->getRecipients($validated['recipients'], $validated['user_id']);
        
        $notificationData = [
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'sender' => auth()->user()->name,
            'sender_role' => 'Admin',
            'send_email' => $validated['send_email'] ?? false
        ];

        // Use chunked processing to prevent timeout
        $userCount = $usersQuery->count();
        $usersQuery->chunk(50, function ($userChunk) use ($notificationData, $validated) {
            foreach ($userChunk as $user) {
                try {
                    $user->notify(new AdminAnnouncement($notificationData));
                    if ($validated['send_email'] ?? false) {
                        \App\Jobs\SendNotificationEmail::dispatch($user, $notificationData);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send notification to user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification sent to ' . $userCount . ' users successfully!'
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification sent to ' . $userCount . ' users successfully!');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function clearAll()
    {
        auth()->user()->notifications()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared'
        ]);
    }

    private function getRecipients($type, $userId = null)
    {
        switch ($type) {
            case 'teachers':
                return User::where('role', 'teacher');
            case 'learners':
                return User::where('role', 'learner');
            case 'admins':
                return User::where('role', 'admin');
            case 'new_users':
                return User::where('created_at', '>=', now()->subDays(7));
            case 'specific':
                return User::where('id', $userId);
            default:
                return User::query();
        }
    }
}