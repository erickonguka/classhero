@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Courses</h1>
        <a href="{{ route('teacher.courses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            Create New Course
        </a>
    </div>

    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                        @if($course->getFirstMediaUrl())
                            <img src="{{ $course->getFirstMediaUrl() }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-{{ $course->category->color ?? 'blue' }}-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $course->category->name }}
                            </span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-{{ $course->status === 'published' ? 'green' : 'yellow' }}-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $course->short_description }}</p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $course->lessons->count() }} lessons
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $course->enrolled_count }} students
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                View
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course) }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No courses yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Create your first course to start teaching</p>
            <a href="{{ route('teacher.courses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Create Your First Course
            </a>
        </div>
    @endif
</div>
@endsection