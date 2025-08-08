@extends('layouts.teacher')

@section('title', 'Lesson Comments')
@section('page-title', 'Lesson Comments')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <p class="text-gray-600 dark:text-gray-400">{{ $lesson->course->title }} • {{ $lesson->title }}</p>
        <a href="{{ route('teacher.courses.show', $lesson->course) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
            ← Back to Course
        </a>
    </div>

    @if($discussions->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Student Comments & Questions</h2>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($discussions as $discussion)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            @if($discussion->user->getProfilePictureUrl())
                                <img src="{{ $discussion->user->getProfilePictureUrl() }}" alt="{{ $discussion->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr($discussion->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $discussion->user->name }}</h4>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $discussion->created_at->diffForHumans() }}</span>
                                    @if($discussion->is_resolved)
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Resolved</span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-700 dark:text-gray-300 mb-3">{{ $discussion->content }}</p>
                                
                                @if($discussion->replies->count() > 0)
                                    <div class="ml-4 border-l-2 border-gray-200 dark:border-gray-600 pl-4 space-y-3">
                                        @foreach($discussion->replies as $reply)
                                            <div class="flex items-start space-x-3">
                                                @if($reply->user->getProfilePictureUrl())
                                                    <img src="{{ $reply->user->getProfilePictureUrl() }}" alt="{{ $reply->user->name }}" class="w-8 h-8 rounded-full object-cover">
                                                @else
                                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-xs font-medium">{{ substr($reply->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                                        @if($reply->user->role === 'teacher')
                                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Teacher</span>
                                                        @endif
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->content }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div class="mt-4 flex items-center space-x-4">
                                    <button onclick="showReplyForm({{ $discussion->id }})" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                                        Reply
                                    </button>
                                    @if(!$discussion->is_resolved)
                                        <form method="POST" action="{{ route('teacher.discussions.resolve', $discussion) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-700 dark:text-green-400 text-sm font-medium">
                                                Mark as Resolved
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <div id="reply-form-{{ $discussion->id }}" class="hidden mt-4">
                                    <form method="POST" action="{{ route('teacher.discussions.reply', $discussion) }}">
                                        @csrf
                                        <textarea name="content" rows="3" placeholder="Write your reply..." required
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                                        <div class="mt-2 flex justify-end space-x-2">
                                            <button type="button" onclick="hideReplyForm({{ $discussion->id }})" class="text-gray-600 hover:text-gray-700 dark:text-gray-400 text-sm">
                                                Cancel
                                            </button>
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                                Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $discussions->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No comments yet</h3>
            <p class="text-gray-600 dark:text-gray-400">Student comments and questions will appear here</p>
        </div>
    @endif
</div>

<script>
function showReplyForm(discussionId) {
    document.getElementById(`reply-form-${discussionId}`).classList.remove('hidden');
}

function hideReplyForm(discussionId) {
    document.getElementById(`reply-form-${discussionId}`).classList.add('hidden');
}
</script>
@endsection