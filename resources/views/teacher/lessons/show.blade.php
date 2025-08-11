@extends('layouts.teacher')

@section('title', $lesson->title)
@section('page-title', $lesson->title)

@section('content')
<div class="p-6">
    <!-- Lesson Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $lesson->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $lesson->course->title }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('teacher.courses.lessons.index', $lesson->course) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Back to Lessons
                </a>
                <a href="{{ route('teacher.courses.show', $lesson->course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Back to Course
                </a>
            </div>
        </div>

        @if($lesson->description)
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">{{ $lesson->description }}</p>
            </div>
        @endif

        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Order: {{ $lesson->order }}</span>
            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">{{ ucfirst($lesson->type) }}</span>
            @if($lesson->duration_minutes)
                <span>{{ $lesson->duration_minutes }} minutes</span>
            @endif
            @if($lesson->quiz)
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Has Quiz</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Lesson Content -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Lesson Content</h2>
                
                @if($lesson->type === 'video' && $lesson->video_url)
                    <div class="mb-4">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                            <iframe src="{{ $lesson->video_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                @elseif($lesson->type === 'audio' && $lesson->getFirstMediaUrl('audio'))
                    <div class="mb-4">
                        <audio controls class="w-full">
                            <source src="{{ $lesson->getFirstMediaUrl('audio') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @elseif($lesson->type === 'pdf' && $lesson->getFirstMediaUrl('pdfs'))
                    <div class="mb-4">
                        <a href="{{ $lesson->getFirstMediaUrl('pdfs') }}" target="_blank" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View PDF
                        </a>
                    </div>
                @endif

                <!-- Lesson Media Items -->
                @if($lesson->lessonMedia->count() > 0)
                    <div class="space-y-4 mb-6">
                        @foreach($lesson->lessonMedia as $media)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                @if($media->title || $media->description)
                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                        @if($media->title)
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $media->title }}</h4>
                                        @endif
                                        @if($media->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $media->description }}</p>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="p-4">
                                    @if($media->type === 'youtube')
                                        <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                            <iframe src="{{ str_replace('watch?v=', 'embed/', $media->url) }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    @elseif($media->type === 'video')
                                        <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                            <video class="w-full h-full" controls>
                                                <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                            </video>
                                        </div>
                                    @elseif($media->type === 'audio')
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-6">
                                            <audio class="w-full" controls>
                                                <source src="{{ asset('storage/' . $media->file_path) }}" type="audio/mpeg">
                                            </audio>
                                        </div>
                                    @elseif($media->type === 'image')
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->title }}" class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                                        </div>
                                    @elseif($media->type === 'pdf')
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                            <iframe src="{{ asset('storage/' . $media->file_path) }}#toolbar=1" width="100%" height="600px"></iframe>
                                        </div>
                                    @elseif($media->type === 'document')
                                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 text-center">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">{{ $media->title ?: 'Document' }}</p>
                                            <a href="{{ asset('storage/' . $media->file_path) }}" download class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors inline-block">
                                                Download
                                            </a>
                                        </div>
                                    @elseif($media->type === 'link')
                                        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-lg p-6 text-center text-white">
                                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            <p class="text-lg font-medium mb-4">{{ $media->title ?: 'External Resource' }}</p>
                                            <a href="{{ $media->url }}" target="_blank" class="bg-white text-green-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors inline-block">
                                                Open Link
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($lesson->content)
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $lesson->content !!}
                    </div>
                @endif
            </div>

            <!-- Quiz Section -->
            @if($lesson->quiz)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Quiz</h2>
                        <a href="{{ route('teacher.quizzes.edit', $lesson->quiz) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Edit Quiz
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Questions</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lesson->quiz->questions->count() }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Passing Score</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lesson->quiz->passing_score }}%</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Time Limit</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lesson->quiz->time_limit ?? 'None' }}</div>
                        </div>
                    </div>

                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Questions Preview</h3>
                    <div class="space-y-3">
                        @foreach($lesson->quiz->questions->take(3) as $question)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Question {{ $question->order }}</span>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($question->question, 100) }}</p>
                            </div>
                        @endforeach
                        @if($lesson->quiz->questions->count() > 3)
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                And {{ $lesson->quiz->questions->count() - 3 }} more questions...
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Comments Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Student Comments</h2>
                    <a href="{{ route('teacher.lessons.comments', $lesson) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Manage Comments
                    </a>
                </div>

                @php
                    $recentComments = $lesson->discussions()->with('user')->latest()->take(3)->get();
                @endphp

                @if($recentComments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentComments as $comment)
                            <div class="flex items-start space-x-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                @if($comment->user->getProfilePictureUrl())
                                    <img src="{{ $comment->user->getProfilePictureUrl() }}" alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-medium">{{ substr($comment->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">{{ Str::limit($comment->content, 150) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">No comments yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-center font-medium transition-colors">
                        Edit Lesson
                    </a>
                    @if(!$lesson->quiz)
                        <a href="{{ route('teacher.lessons.quiz.create', $lesson) }}" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-center font-medium transition-colors">
                            Add Quiz
                        </a>
                    @else
                        <a href="{{ route('teacher.quizzes.edit', $lesson->quiz) }}" class="block bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg text-center font-medium transition-colors">
                            Edit Quiz
                        </a>
                    @endif
                    <button onclick="confirmDeleteLesson('{{ $lesson->slug }}', '{{ addslashes($lesson->title) }}')" class="block w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg text-center font-medium transition-colors">
                        Delete Lesson
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Comments</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $lesson->discussions->count() }}</span>
                    </div>
                    @if($lesson->quiz)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Quiz Attempts</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $lesson->quiz->attempts->count() }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Created</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $lesson->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Course Navigation -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Course Lessons</h3>
                <div class="space-y-2">
                    @foreach($lesson->course->lessons->sortBy('order') as $courseLesson)
                        <div class="flex items-center space-x-3 p-2 rounded-lg {{ $courseLesson->id === $lesson->id ? 'bg-blue-50 dark:bg-blue-900' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <span class="text-sm font-medium {{ $courseLesson->id === $lesson->id ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $courseLesson->order }}</span>
                            @if($courseLesson->id === $lesson->id)
                                <span class="flex-1 text-sm font-medium text-blue-600 dark:text-blue-400">{{ $courseLesson->title }}</span>
                            @else
                                <a href="{{ route('teacher.lessons.show', $courseLesson) }}" class="flex-1 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $courseLesson->title }}
                                </a>
                            @endif
                            @if($courseLesson->quiz)
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteLesson(lessonSlug, lessonTitle) {
    Swal.fire({
        title: 'Delete Lesson',
        text: `Are you sure you want to delete "${lessonTitle}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteLesson(lessonSlug);
        }
    });
}

function deleteLesson(lessonSlug) {
    fetch(`/teacher/lessons/${lessonSlug}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Deleted!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                window.location.href = data.redirect;
            });
        } else {
            throw new Error(data.message || 'Failed to delete lesson');
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: error.message,
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    });
}
</script>
@endsection