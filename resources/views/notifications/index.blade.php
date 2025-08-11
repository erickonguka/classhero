@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : (auth()->user()->role === 'teacher' ? 'layouts.teacher' : 'layouts.app'))

@section('title', 'Notifications')
@section('page-title', 'My Notifications')

@section('content')
<div class="bg-gradient-to-br from-indigo-50 to-purple-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Notifications</h2>
                    <p class="text-gray-600 dark:text-gray-400">Stay updated with your latest notifications</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button onclick="markAllAsRead()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Mark All Read
                    </button>
                    <button onclick="clearAll()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                @if($notifications->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($notifications as $notification)
                            <div class="p-4 sm:p-6 {{ $notification->read_at ? '' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                            <a href="{{ route('notifications.show', $notification->id) }}" class="text-base sm:text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 transition-colors truncate">
                                                @php
                                                    $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                                                @endphp
                                                {{ $notification->title ?? $data['title'] ?? $notification->data['title'] ?? 'Notification' }}
                                            </a>
                                            @if(!$notification->read_at)
                                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full self-start">New</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm sm:text-base">
                                            @php
                                                $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                                            @endphp
                                            {{ strip_tags($notification->message ?? $data['message'] ?? $notification->data['message'] ?? 'No message') }}
                                        </p>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            @if(isset($data['sender']) || isset($notification->data['sender']))
                                                <span class="truncate">From: 
                                                    @if(isset($data['sender_role']) || isset($notification->data['sender_role']))
                                                        {{ $data['sender_role'] ?? $notification->data['sender_role'] }} {{ $data['sender'] ?? $notification->data['sender'] }}
                                                    @else
                                                        {{ $data['sender'] ?? $notification->data['sender'] }}
                                                    @endif
                                                </span>
                                            @endif
                                            @if(isset($data['type']) || isset($notification->data['type']))
                                                @php $type = $data['type'] ?? $notification->data['type']; @endphp
                                                <span class="px-2 py-1 rounded-full text-xs self-start {{ 
                                                    $type === 'alert' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                    ($type === 'announcement' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') 
                                                }}">
                                                    {{ ucfirst($type) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-2 self-start">
                                        @if(!$notification->read_at)
                                            <button onclick="markAsRead('{{ $notification->id }}')" 
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                                                Mark Read
                                            </button>
                                        @endif
                                        <button onclick="deleteNotification('{{ $notification->id }}')" 
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="p-8 sm:p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v5"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications</h3>
                        <p class="text-gray-600 dark:text-gray-400">You don't have any notifications yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        }
    });
}

function clearAll() {
    Swal.fire({
        title: 'Clear All Notifications',
        text: 'This will permanently delete all notifications. Continue?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, clear all!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/notifications/clear-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    location.reload();
                }
            });
        }
    });
}

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'GET'
    })
    .then(() => {
        location.reload();
    });
}

function deleteNotification(notificationId) {
    Swal.fire({
        title: 'Delete Notification',
        text: 'Are you sure you want to delete this notification?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Notification deleted');
                    location.reload();
                }
            });
        }
    });
}
</script>
@endpush
@endsection