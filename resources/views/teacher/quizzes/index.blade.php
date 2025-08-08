@extends('layouts.teacher')

@section('title', 'Quiz Management')
@section('page-title', 'Quiz Management')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-gray-600 dark:text-gray-400">Manage quizzes across all your courses</p>
    </div>

    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search quizzes..." 
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            <select name="course_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    @if($quizzes->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Quizzes</h2>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($quizzes as $quiz)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quiz->title }}</h3>
                                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-medium">
                                        {{ $quiz->questions->count() }} Questions
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <span class="font-medium">Course:</span> {{ $quiz->lesson->course->title }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <span class="font-medium">Lesson:</span> {{ $quiz->lesson->title }}
                                </p>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>Passing Score: {{ $quiz->passing_score }}%</span>
                                    <span>Max Attempts: {{ $quiz->max_attempts }}</span>
                                    @if($quiz->time_limit)
                                        <span>Time Limit: {{ $quiz->time_limit }} min</span>
                                    @endif
                                    <span>Attempts: {{ $quiz->attempts->count() }}</span>
                                </div>
                            </div>

                            <div class="flex space-x-2 ml-4">
                                <a href="{{ route('teacher.quizzes.show', $quiz) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    View Details
                                </a>
                                <a href="{{ route('teacher.quizzes.edit', $quiz) }}" 
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $quizzes->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No quizzes yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Create lessons first, then add quizzes to them</p>
            <a href="{{ route('teacher.courses.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Go to Courses
            </a>
        </div>
    @endif
</div>
@endsection