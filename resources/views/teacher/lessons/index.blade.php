@extends('layouts.app')

@section('title', 'Manage Lessons - ' . $course->title)

@section('content')
<div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Lessons</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $course->title }}</p>
            </div>
            <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Add New Lesson
            </a>
        </div>

        <!-- Lessons List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            @if($lessons->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($lessons as $lesson)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                            {{ $lesson->order }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $lesson->title }}</h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                                                <span class="capitalize">{{ $lesson->type }}</span>
                                                @if($lesson->duration_minutes)
                                                    <span>{{ $lesson->duration_minutes }} minutes</span>
                                                @endif
                                                @if($lesson->is_free)
                                                    <span class="text-green-600 dark:text-green-400">Free</span>
                                                @endif
                                                @if($lesson->quiz)
                                                    <span class="text-purple-600 dark:text-purple-400">Has Quiz</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('teacher.lessons.show', $lesson) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                                        Edit
                                    </a>
                                    @if(!$lesson->quiz)
                                        <a href="{{ route('teacher.lessons.quiz.create', $lesson) }}" class="text-purple-600 hover:text-purple-700 dark:text-purple-400 font-medium">
                                            Add Quiz
                                        </a>
                                    @endif
                                    <button onclick="deleteLesson({{ $lesson->id }})" class="text-red-600 hover:text-red-700 dark:text-red-400 font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No lessons yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first lesson.</p>
                    <div class="mt-6">
                        <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Create Lesson
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteLesson(lessonId) {
    Swal.fire({
        title: 'Delete Lesson?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $(`<form method="POST" action="/teacher/lessons/${lessonId}">
                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                <input type="hidden" name="_method" value="DELETE">
            </form>`).appendTo('body').submit();
        }
    });
}
</script>
@endpush