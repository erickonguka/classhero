@extends('layouts.app')

@section('title', 'Course Details - ' . $course->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Course Details</h1>
        <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
            ← Back to Courses
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Course Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-start space-x-4">
                    @if($course->getFirstMediaUrl('thumbnails'))
                        <img src="{{ $course->getFirstMediaUrl('thumbnails') }}" alt="{{ $course->title }}" class="w-32 h-24 rounded-lg object-cover">
                    @else
                        <div class="w-32 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ substr($course->title, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $course->short_description }}</p>
                        <div class="flex items-center space-x-4 text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $course->category->name }}</span>
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ ucfirst($course->difficulty) }}</span>
                            <span class="px-2 py-1 rounded
                                {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $course->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $course->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Course Description</h3>
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($course->description)) !!}
                </div>
            </div>

            @if($course->lessons->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lessons ({{ $course->lessons->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($course->lessons as $lesson)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $lesson->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($lesson->type) }} • {{ $lesson->duration_minutes ?? 'N/A' }} min</p>
                                </div>
                                <span class="text-sm text-gray-500">Order: {{ $lesson->order }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($course->rejection_reason)
                <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">Rejection Reason</h3>
                    <p class="text-red-700 dark:text-red-300">{{ $course->rejection_reason }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            @if($course->status === 'pending')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                    <div class="space-y-3">
                        <form action="{{ route('admin.courses.approve', $course) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                Approve Course
                            </button>
                        </form>
                        <button onclick="showRejectModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Reject Course
                        </button>
                    </div>
                </div>
            @endif

            <!-- Teacher Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Teacher</h3>
                <div class="flex items-center space-x-3">
                    @if($course->teacher->getProfilePictureUrl())
                        <img src="{{ $course->teacher->getProfilePictureUrl() }}" alt="{{ $course->teacher->name }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($course->teacher->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $course->teacher->name }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $course->teacher->email }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Member since {{ $course->teacher->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Course Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Students</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $course->enrolled_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Lessons</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $course->lessons->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Price</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $course->is_free ? 'Free' : '$' . number_format($course->price, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Created</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $course->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reject Course</h3>
            <form action="{{ route('admin.courses.reject', $course) }}" method="POST">
                @csrf
                <textarea name="reason" rows="4" placeholder="Reason for rejection..." required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endsection