@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Notification Center</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage and send notifications to users</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="markAllAsRead()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Mark All Read
            </button>
            <button onclick="clearAll()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                Clear All
            </button>
            <a href="{{ route('admin.notifications.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Send Notification
            </a>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <div class="p-6 {{ $notification->read_at ? '' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                @php
                                    $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                                    $title = $data['title'] ?? $notification->type ?? 'Notification';
                                    $message = $data['message'] ?? 'No message';
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $title }}
                                    </h3>
                                    @if(!$notification->read_at)
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">New</span>
                                    @endif
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    {{ strip_tags($message) }}
                                </p>
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    @if(isset($data['sender']))
                                        <span>From: {{ $data['sender'] }}</span>
                                    @endif
                                    @if(isset($data['type']))
                                        <span class="px-2 py-1 rounded-full text-xs {{ 
                                            $data['type'] === 'alert' ? 'bg-red-100 text-red-800' : 
                                            ($data['type'] === 'announcement' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') 
                                        }}">
                                            {{ ucfirst($data['type']) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                @if(!$notification->read_at)
                                    <button onclick="markAsRead('{{ $notification->id }}')" 
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                                        Mark Read
                                    </button>
                                @endif
                                <button onclick="deleteNotification('{{ $notification->id }}')" 
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v5"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications</h3>
                <p class="text-gray-600 dark:text-gray-400">You don't have any notifications yet.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAllAsRead() {
    fetch('/admin/notifications/mark-all-read', {
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
            fetch('/admin/notifications/clear-all', {
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
    // This would need a delete route
    toastr.info('Delete functionality coming soon');
}
</script>
@endpush
@endsection