@extends('layouts.teacher')

@section('title', 'Manage Comments - ' . $course->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Comments</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $course->title }}</p>
        </div>
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
            ← Back to Course
        </a>
    </div>

    @if($pendingComments->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Pending Comments ({{ $pendingComments->total() }})</h2>
            </div>
            
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($pendingComments as $comment)
                    <div class="p-6" id="comment-{{ $comment->id }}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr($comment->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->user->email }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <strong>Lesson:</strong> {{ $comment->lesson->title }}
                            </p>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-900 dark:text-white">{{ $comment->content }}</p>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button onclick="approveComment({{ $comment->id }})" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Approve
                            </button>
                            <button onclick="rejectComment({{ $comment->id }})" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Reject
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            {{ $pendingComments->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No pending comments</h3>
            <p class="text-gray-600 dark:text-gray-400">All comments have been reviewed. New comments will appear here for approval.</p>
        </div>
    @endif
</div>

<script>
function approveComment(commentId) {
    if (!confirm('Are you sure you want to approve this comment?')) return;
    
    fetch(`/teacher/discussions/${commentId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`comment-${commentId}`).remove();
            toastr.success(data.message);
            
            // Check if no more comments
            if (document.querySelectorAll('[id^="comment-"]').length === 0) {
                location.reload();
            }
        } else {
            toastr.error(data.error || 'Failed to approve comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred');
    });
}

function rejectComment(commentId) {
    if (!confirm('Are you sure you want to reject this comment?')) return;
    
    fetch(`/teacher/discussions/${commentId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`comment-${commentId}`).remove();
            toastr.success(data.message);
            
            // Check if no more comments
            if (document.querySelectorAll('[id^="comment-"]').length === 0) {
                location.reload();
            }
        } else {
            toastr.error(data.error || 'Failed to reject comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred');
    });
}
</script>
@endsection