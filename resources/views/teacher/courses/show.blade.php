@extends('layouts.teacher')

@section('title', $course->title)
@section('page-title', $course->title)

@section('content')
<div class="p-6">
    <!-- Course Header with Thumbnail -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
        @if($course->getFirstMediaUrl('thumbnails'))
            <div class="h-64 bg-cover bg-center" style="background-image: url('{{ $course->getFirstMediaUrl('thumbnails') }}')">
                <div class="h-full bg-black bg-opacity-40 flex items-end">
                    <div class="p-6 text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $course->title }}</h1>
                        <p class="text-gray-200">{{ $course->short_description }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-end">
                <div class="p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ $course->title }}</h1>
                    <p class="text-gray-200">{{ $course->short_description }}</p>
                </div>
            </div>
        @endif
        
        <div class="p-6">
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                    Preview
                </a>
                <a href="{{ route('teacher.courses.edit', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                    Edit
                </a>
                <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                    Add Lesson
                </a>
                @if($course->status === 'draft' && $course->canBePublished())
                    <form action="{{ route('teacher.courses.publish', $course) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            Submit
                        </button>
                    </form>
                @elseif($course->status === 'draft')
                    <button disabled class="bg-gray-400 text-white px-3 py-1 rounded text-sm font-medium cursor-not-allowed" title="Add at least 1 lesson to publish">
                        Submit
                    </button>
                @endif
                <a href="{{ route('teacher.courses.students', $course) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                    Students ({{ $course->enrollments->count() }})
                </a>
                <a href="{{ route('teacher.courses.payments', $course) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                    Payments
                </a>
            </div>
            
            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-4">
                <span class="bg-{{ $course->category->color ?? 'blue' }}-100 text-{{ $course->category->color ?? 'blue' }}-800 px-2 py-1 rounded">{{ $course->category->name }}</span>
                <span>{{ ucfirst($course->difficulty) }}</span>
                <span>{{ $course->is_free ? 'Free' : '$' . number_format($course->price, 2) }}</span>
                <span class="bg-{{ $course->status === 'published' ? 'green' : 'yellow' }}-100 text-{{ $course->status === 'published' ? 'green' : 'yellow' }}-800 px-2 py-1 rounded">{{ ucfirst($course->status) }}</span>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['total_enrollments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Lessons</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['total_lessons'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['completion_rate'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Rating</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analytics['average_rating'], 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lessons Management -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Course Lessons</h2>
            <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Add New Lesson
            </a>
        </div>

        @if($course->lessons->count() > 0)
            <div class="space-y-4">
                @foreach($course->lessons as $lesson)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $lesson->title }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">{{ $lesson->description }}</p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>Order: {{ $lesson->order }}</span>
                                    <span>Type: {{ ucfirst($lesson->type) }}</span>
                                    @if($lesson->duration_minutes)
                                        <span>{{ $lesson->duration_minutes }} minutes</span>
                                    @endif
                                    @if($lesson->quiz)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Has Quiz</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Edit
                                </a>
                                @if(!$lesson->quiz)
                                    <a href="{{ route('teacher.lessons.quiz.create', $lesson) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Add Quiz
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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

    <!-- Students Management -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Enrolled Students</h2>
            <a href="{{ route('teacher.courses.students', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                View All Students
            </a>
        </div>

        @if($course->enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Enrolled</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($course->enrollments->take(5) as $enrollment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($enrollment->user->getProfilePictureUrl())
                                            <img src="{{ $enrollment->user->getProfilePictureUrl() }}" alt="{{ $enrollment->user->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $enrollment->progress_percentage }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $enrollment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $enrollment->is_banned ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $enrollment->is_banned ? 'Banned' : 'Active' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No students enrolled yet</h3>
                <p class="text-gray-600 dark:text-gray-400">Students will appear here once they enroll in your course</p>
            </div>
        @endif
    </div>

    <!-- Course Comments Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Course Comments by Lesson</h2>
        
        @if($course->lessons->count() > 0)
            <div class="space-y-6">
                @foreach($course->lessons as $lesson)
                    @php
                        $discussions = $lesson->discussions()->with('user')->latest()->take(3)->get();
                    @endphp
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $lesson->title }}</h3>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $discussions->count() }} comments</span>
                        </div>
                        
                        @if($discussions->count() > 0)
                            <div class="space-y-3 mb-4">
                                @foreach($discussions as $discussion)
                                    <div class="flex items-start space-x-3 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        @if($discussion->user->getProfilePictureUrl())
                                            <img src="{{ $discussion->user->getProfilePictureUrl() }}" alt="{{ $discussion->user->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-medium">{{ substr($discussion->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $discussion->user->name }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $discussion->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($discussion->content, 100) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <a href="{{ route('teacher.lessons.comments', $lesson) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                                View all comments →
                            </a>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No comments yet</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No lessons available</p>
        @endif
    </div>

    <!-- Course Comments Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Course Comments by Lesson</h2>
        
        @if($course->lessons->count() > 0)
            <div class="space-y-6">
                @foreach($course->lessons as $lesson)
                    @php
                        $discussions = $lesson->discussions()->with('user')->latest()->take(3)->get();
                    @endphp
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $lesson->title }}</h3>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $discussions->count() }} comments</span>
                        </div>
                        
                        @if($discussions->count() > 0)
                            <div class="space-y-3 mb-4">
                                @foreach($discussions as $discussion)
                                    <div class="flex items-start space-x-3 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        @if($discussion->user->getProfilePictureUrl())
                                            <img src="{{ $discussion->user->getProfilePictureUrl() }}" alt="{{ $discussion->user->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-medium">{{ substr($discussion->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $discussion->user->name }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $discussion->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($discussion->content, 100) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <a href="{{ route('teacher.lessons.comments', $lesson) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                                View all comments →
                            </a>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No comments yet</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No lessons available</p>
        @endif
    </div>
</div>
@endsection