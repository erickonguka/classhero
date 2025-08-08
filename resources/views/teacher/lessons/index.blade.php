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
            <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Add New Lesson
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            @if($lessons->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($lessons as $lesson)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $lesson->title }}
                                    </h3>
                                    @if($lesson->description)
                                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($lesson->description, 100) }}</p>
                                    @endif
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span>Order: {{ $lesson->order }}</span>
                                        @if($lesson->duration_minutes)
                                            <span>{{ $lesson->duration_minutes }} minutes</span>
                                        @endif
                                        <span>{{ $lesson->lessonMedia->count() }} media items</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('teacher.courses.lessons.edit', [$course, $lesson]) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('teacher.courses.lessons.destroy', [$course, $lesson]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400" onclick="return confirm('Are you sure you want to delete this lesson?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No lessons yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first lesson.</p>
                    <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Create First Lesson
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection