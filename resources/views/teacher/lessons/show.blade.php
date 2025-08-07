@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $lesson->title }}</h1>
            <a href="{{ route('teacher.courses.show', $lesson->course) }}" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                ← Back to Course
            </a>
        </div>

        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-400">{{ $lesson->description }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Lesson Details</h3>
                <div class="space-y-2 text-sm">
                    <p><strong>Type:</strong> {{ ucfirst($lesson->type) }}</p>
                    <p><strong>Order:</strong> {{ $lesson->order }}</p>
                    @if($lesson->duration_minutes)
                        <p><strong>Duration:</strong> {{ $lesson->duration_minutes }} minutes</p>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                        Edit Lesson
                    </a>
                    @if(!$lesson->quiz)
                        <a href="{{ route('teacher.lessons.quiz.create', $lesson) }}" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                            Add Quiz
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection