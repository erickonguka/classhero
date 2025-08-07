@extends('layouts.app')

@section('title', 'My Progress')

@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">My Learning Progress</h1>
            <p class="text-gray-600 dark:text-gray-400">Track your course progress and achievements</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_enrolled'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Enrolled Courses</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed_courses'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Completed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['in_progress'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">In Progress</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_points'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Points Earned</div>
            </div>
        </div>

        <!-- Course Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Course Progress</h2>
            
            @if($enrollments->count() > 0)
                <div class="space-y-6">
                    @foreach($enrollments as $enrollment)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        <a href="{{ route('courses.show', $enrollment->course->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $enrollment->course->title }}
                                        </a>
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">
                                            {{ $enrollment->course->category->name }}
                                        </span>
                                        <span>{{ $enrollment->course->lessons->count() }} lessons</span>
                                        <span>Enrolled {{ $enrollment->enrolled_at->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 lg:mt-0 lg:ml-6">
                                    @if($enrollment->progress_percentage == 100)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Completed
                                        </span>
                                    @elseif($enrollment->progress_percentage > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                            In Progress
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            Not Started
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3">
                                @if($enrollment->course->lessons->count() > 0)
                                    @php
                                        $nextLesson = $enrollment->course->lessons->first();
                                        foreach($enrollment->course->lessons as $lesson) {
                                            $progress = auth()->user()->lessonProgress()->where('lesson_id', $lesson->id)->first();
                                            if (!$progress || !$progress->is_completed) {
                                                $nextLesson = $lesson;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <a href="{{ route('lessons.show', [$enrollment->course->slug, $nextLesson->slug]) }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        {{ $enrollment->progress_percentage > 0 ? 'Continue Learning' : 'Start Course' }}
                                    </a>
                                @endif
                                
                                @if($enrollment->progress_percentage == 100 && $enrollment->course->has_certificate)
                                    <form action="{{ route('certificate.generate', $enrollment->course) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Get Certificate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No courses enrolled</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by enrolling in your first course.</p>
                    <div class="mt-6">
                        <a href="{{ route('courses.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Browse Courses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection