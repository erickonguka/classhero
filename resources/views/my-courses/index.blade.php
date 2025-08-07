@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">My Courses</h1>
            <p class="text-gray-600 dark:text-gray-400">Continue your learning journey</p>
        </div>

        @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrollments as $enrollment)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                            <div class="absolute top-4 left-4">
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $enrollment->course->category->name }}
                                </span>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <div class="bg-white bg-opacity-90 rounded-full px-3 py-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $enrollment->course->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">{{ Str::limit($enrollment->course->short_description, 100) }}</p>
                            
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
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">{{ substr($enrollment->course->teacher->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $enrollment->course->teacher->name }}</span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $enrollment->course->lessons->count() }} lessons
                                </span>
                            </div>
                            
                            <div class="flex space-x-2">
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
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                                        {{ $enrollment->progress_percentage > 0 ? 'Continue' : 'Start' }}
                                    </a>
                                @endif
                                <a href="{{ route('courses.show', $enrollment->course->slug) }}" 
                                   class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($enrollments->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $enrollments->links() }}
                </div>
            @endif
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
@endsection