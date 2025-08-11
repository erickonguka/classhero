@extends('layouts.teacher')

@section('title', 'Course Lessons')
@section('page-title', 'Course Lessons')

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('teacher.courses.show', $course) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                    ← Back to Course
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $course->title }} - Lessons</h1>
            </div>
            <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Add New Lesson
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            @if($lessons->count() > 0)
                <div class="space-y-4">
                    @foreach($lessons as $lesson)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-3 lg:space-y-0">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $lesson->title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-2 text-sm">{{ \Illuminate\Support\Str::limit($lesson->description, 100) }}</p>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Order: {{ $lesson->order }}</span>
                                        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ ucfirst($lesson->type) }}</span>
                                        @if($lesson->duration_minutes)
                                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $lesson->duration_minutes }}m</span>
                                        @endif
                                        @if($lesson->lessonMedia->count() > 0)
                                            @php
                                                $mediaTypes = $lesson->lessonMedia->pluck('type')->unique();
                                            @endphp
                                            @foreach($mediaTypes as $mediaType)
                                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ ucfirst($mediaType) }}</span>
                                            @endforeach
                                        @endif
                                        @if($lesson->quiz)
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Has Quiz</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1 lg:ml-4">
                                    <a href="{{ route('teacher.lessons.show', $lesson) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                        Edit
                                    </a>
                                    @if(!$lesson->quiz)
                                        <a href="{{ route('teacher.lessons.quiz.create', $lesson) }}" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Quiz
                                        </a>
                                    @else
                                        <a href="{{ route('teacher.quizzes.edit', $lesson->quiz) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Edit Quiz
                                        </a>
                                    @endif
                                    <button onclick="confirmDeleteLesson('{{ $lesson->slug }}', '{{ addslashes($lesson->title) }}')" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($lessons->hasPages())
                    <div class="mt-6">
                        {{ $lessons->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No lessons yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Add your first lesson to get started</p>
                    <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Add First Lesson
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

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
                location.reload();
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