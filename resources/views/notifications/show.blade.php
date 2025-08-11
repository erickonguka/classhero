@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : (auth()->user()->role === 'teacher' ? 'layouts.teacher' : 'layouts.app'))

@section('title', 'Notification')
@section('page-title', 'Notification Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                        $title = $data['title'] ?? $notification->type ?? 'Notification';
                        $message = $data['message'] ?? 'No message content';
                    @endphp
                    <h1 class="text-xl font-bold text-white">{{ $title }}</h1>
                    <div class="flex items-center space-x-4 mt-2 text-blue-100">
                        <span class="text-sm">
                            @if(isset($data['sender_role']))
                                {{ $data['sender_role'] }}: {{ $data['sender'] ?? 'System' }}
                            @else
                                From: {{ $data['sender'] ?? 'System' }}
                            @endif
                        </span>
                        <span class="text-sm">{{ $notification->created_at->format('M d, Y \a\t g:i A') }}</span>
                        @if(isset($data['type']))
                            <span class="px-2 py-1 rounded-full text-xs bg-white bg-opacity-20">
                                {{ ucfirst($data['type']) }}
                            </span>
                        @endif
                    </div>
                </div>
                @if(!$notification->read_at)
                    <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full">New</span>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="prose dark:prose-invert max-w-none">
                {!! $message !!}
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-between items-center">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                @if($notification->read_at)
                    Read on {{ $notification->read_at->format('M d, Y \a\t g:i A') }}
                @else
                    <span class="text-blue-600 dark:text-blue-400">Unread</span>
                @endif
            </div>
            <div class="flex space-x-3">
                @if(!$notification->read_at)
                    <button onclick="markAsRead('{{ $notification->id }}')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                        Mark as Read
                    </button>
                @endif
                <button onclick="deleteNotification('{{ $notification->id }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 text-sm">
                    Delete
                </button>
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.notifications.index') : route('notifications.index') }}" 
                   class="text-gray-600 hover:text-gray-800 dark:text-gray-400 text-sm">
                    Back to Notifications
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
                    window.history.back();
                }
            });
        }
    });
}
</script>
@endpush
@endsection