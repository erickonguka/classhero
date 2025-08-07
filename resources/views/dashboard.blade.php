@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600 dark:text-gray-400">Continue your learning journey</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Enrolled Courses</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->enrollments()->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed Lessons</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->lessonProgress()->where('is_completed', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Points Earned</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->points }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Hours Learned</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format(auth()->user()->lessonProgress()->sum('time_spent') / 3600, 1) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Continue Learning Section -->
        @if(auth()->user()->enrollments()->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Continue Learning</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(auth()->user()->enrollments()->with('course.lessons')->latest()->take(3)->get() as $enrollment)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="h-32 bg-gradient-to-br from-blue-500 to-purple-600"></div>
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $enrollment->course->title }}</h3>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-4">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                                <a href="{{ route('courses.show', $enrollment->course->slug) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center block">
                                    Continue Course
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recommended Courses -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recommended for You</h2>
                <a href="{{ route('courses.index') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    View All Courses
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(\App\Models\Course::where('status', 'published')->where('is_free', true)->with(['teacher', 'category'])->take(4)->get() as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div class="h-32 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                            <div class="absolute top-2 left-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">Free</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">{{ Str::limit($course->title, 40) }}</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">{{ $course->teacher->name }}</p>
                            <a href="{{ route('courses.show', $course->slug) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors text-center block">
                                View Course
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 text-white text-center">
            <h2 class="text-2xl font-bold mb-4">Ready to Learn Something New?</h2>
            <p class="text-blue-100 mb-6">Explore our course catalog and find your next learning adventure</p>
            <a href="{{ route('courses.index') }}" 
               class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Browse All Courses
            </a>
        </div>
    </div>
</div>
@endsection