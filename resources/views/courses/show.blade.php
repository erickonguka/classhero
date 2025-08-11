@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Course Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="mb-4">
                        <span class="bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $course->category->name }}
                        </span>
                        <span class="bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm font-medium ml-2">
                            {{ ucfirst($course->difficulty) }}
                        </span>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $course->title }}</h1>
                    <p class="text-xl text-blue-100 mb-6">{{ $course->short_description }}</p>
                    
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr($course->teacher->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-white font-medium">{{ $course->teacher->name }}</div>
                                <div class="text-blue-200 text-sm">Instructor</div>
                                @if($course->teacher->country_code)
                                    <div class="text-blue-200 text-xs flex items-center mt-1">
                                        <span class="mr-1">{{ $course->teacher->getCountryFlag() }}</span>
                                        {{ $course->teacher->getCountryName() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-1">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="text-white font-medium">{{ number_format($course->rating, 1) }}</span>
                            <span class="text-blue-200">({{ $course->rating_count }} reviews)</span>
                        </div>
                        
                        <div class="text-white">
                            <span class="font-medium">{{ $course->enrolled_count }}</span>
                            <span class="text-blue-200">students</span>
                        </div>
                        
                        <div class="text-white">
                            <span class="text-blue-200">Created:</span>
                            <span class="font-medium">{{ $course->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Course Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 sticky top-24">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg mb-6 relative overflow-hidden">
                            @if($course->getFirstMediaUrl('thumbnails'))
                                <img src="{{ $course->getFirstMediaUrl('thumbnails') }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <p class="text-sm opacity-75">{{ $course->category->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-center mb-6">
                            @if($course->is_free)
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">Free</div>
                            @else
                                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                    @auth
                                        {{ auth()->user()->getCurrencySymbol() }}{{ number_format(\App\Services\CurrencyService::convert($course->price, 'USD', auth()->user()->currency), 2) }}
                                    @else
                                        @php
                                            $guestCurrency = session('guest_currency', 'USD');
                                            $convertedPrice = \App\Services\CurrencyService::convert($course->price, 'USD', $guestCurrency);
                                            $symbol = \App\Services\CurrencyService::getSymbol($guestCurrency);
                                        @endphp
                                        {{ $symbol }}{{ number_format($convertedPrice, 2) }}
                                    @endauth
                                </div>
                            @endif
                        </div>
                        
                        @auth
                            @if($isEnrolled)
                                <div class="space-y-4">
                                    <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-center">
                                        ✓ You are enrolled in this course
                                    </div>
                                    @if($course->lessons->count() > 0)
                                        <a href="{{ route('lessons.show', [$course->slug, $course->lessons->first()->slug]) }}" 
                                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-center block transition-colors">
                                            Continue Learning
                                        </a>
                                    @endif
                                    @if($enrollment)
                                        <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                                            Progress: {{ $enrollment->progress_percentage }}%
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                    @endif
                                    @if($course->has_certificate && $enrollment && $enrollment->progress_percentage == 100)
                                        <form action="{{ route('certificate.generate', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                                                Generate Certificate
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @elseif($canEnroll)
                                @if($course->is_free)
                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                                            Enroll Now
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('payment.checkout', $course) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-center block transition-colors">
                                        Buy Course - {{ auth()->user()->getCurrencySymbol() }}{{ number_format(\App\Services\CurrencyService::convert($course->price, 'USD', auth()->user()->currency), 2) }}
                                    </a>
                                @endif
                            @elseif(auth()->user()->role === 'teacher')
                                <div class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-4 py-3 rounded-lg text-center">
                                    Teachers cannot enroll in courses
                                </div>
                            @else
                                <div class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-4 py-3 rounded-lg text-center">
                                    Enrollment not available
                                </div>
                            @endif
                        @else
                            <div class="space-y-4">
                                <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-center block transition-colors">
                                    Login to Enroll
                                </a>
                                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                                    Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Sign up</a>
                                </p>
                            </div>
                        @endauth
                        
                        <!-- Course Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Lessons</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $course->lessons->count() }}</span>
                            </div>
                            @if($course->duration_hours)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Duration</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $course->duration_hours }} hours</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Level</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($course->difficulty) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ activeTab: 'overview' }">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
                    <nav class="flex space-x-8">
                        <button @click="activeTab = 'overview'" 
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'overview', 'border-transparent text-gray-500': activeTab !== 'overview' }"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Overview
                        </button>
                        <button @click="activeTab = 'curriculum'" 
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'curriculum', 'border-transparent text-gray-500': activeTab !== 'curriculum' }"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Curriculum
                        </button>
                        <button @click="activeTab = 'instructor'" 
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'instructor', 'border-transparent text-gray-500': activeTab !== 'instructor' }"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Instructor
                        </button>
                        <button @click="activeTab = 'reviews'" 
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'reviews', 'border-transparent text-gray-500': activeTab !== 'reviews' }"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Reviews ({{ $course->reviews->count() }})
                        </button>
                    </nav>
                </div>
                
                <!-- Tab Content -->
                <div x-show="activeTab === 'overview'">
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($course->overview ?: $course->description)) !!}
                    </div>
                    

                    
                    @if($course->what_you_learn)
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">What you'll learn</h3>
                            <ul class="space-y-2">
                                @foreach($course->what_you_learn as $item)
                                    <li class="flex items-start space-x-3">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if($course->requirements)
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Requirements</h3>
                            <ul class="space-y-2">
                                @foreach($course->requirements as $requirement)
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $requirement }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                
                <div x-show="activeTab === 'curriculum'">
                    <div class="space-y-4">
                        @foreach($course->lessons as $index => $lesson)
                            @php
                                $isCompleted = $isEnrolled && auth()->user() && auth()->user()->lessonProgress()->where('lesson_id', $lesson->id)->where('is_completed', true)->exists();
                                $canAccess = $lesson->is_free || ($isEnrolled && ($lesson->order == 1 || $isCompleted));
                                
                                // Check if previous lesson is completed for progression
                                if ($isEnrolled && auth()->user() && $lesson->order > 1) {
                                    $previousLesson = $course->lessons()->where('order', '<', $lesson->order)->orderBy('order', 'desc')->first();
                                    if ($previousLesson) {
                                        $previousCompleted = auth()->user()->lessonProgress()->where('lesson_id', $previousLesson->id)->where('is_completed', true)->exists();
                                        $canAccess = $canAccess && $previousCompleted;
                                    }
                                }
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ !$canAccess && $isEnrolled ? 'opacity-60' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                            @if($isCompleted)
                                                bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400
                                            @elseif(!$canAccess && $isEnrolled)
                                                bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400
                                            @else
                                                bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400
                                            @endif
                                        ">
                                            @if($isCompleted)
                                                ✓
                                            @elseif(!$canAccess && $isEnrolled)
                                                🔒
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $lesson->title }}</h4>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <span class="capitalize">{{ $lesson->type }}</span>
                                                @if($lesson->duration_minutes)
                                                    <span>{{ $lesson->duration_minutes }} min</span>
                                                @endif
                                                @if($lesson->is_free)
                                                    <span class="text-green-600 dark:text-green-400">Free</span>
                                                @endif
                                                @if(!$canAccess && $isEnrolled)
                                                    <span class="text-orange-600 dark:text-orange-400">Complete previous lesson</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($canAccess)
                                        <a href="{{ route('lessons.show', [$course->slug, $lesson->slug]) }}" 
                                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                            {{ $isCompleted ? 'Review' : 'Start Lesson' }}
                                        </a>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div x-show="activeTab === 'instructor'">
                    <div class="flex items-start space-x-6">
                        @if($course->teacher->getProfilePictureUrl())
                            <img src="{{ $course->teacher->getProfilePictureUrl() }}" alt="{{ $course->teacher->name }}" class="w-20 h-20 rounded-full object-cover">
                        @else
                            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">{{ substr($course->teacher->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $course->teacher->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">Course Instructor</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Member since {{ $course->teacher->created_at->format('M Y') }}</p>
                            
                            <!-- Instructor Stats -->
                            <div class="flex items-center space-x-6 mb-4 text-sm">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $course->teacher->courses->count() }} courses</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $course->teacher->courses->sum('enrolled_count') }} students</span>
                                </div>
                            </div>
                            
                            <div class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($course->instructor_info ?: $course->teacher->bio ?: 'No instructor information available.')) !!}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Enrolled Students -->
                    @if($course->enrollments->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Students in this course ({{ $course->enrollments->count() }})</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($course->enrollments->take(20) as $enrollment)
                                    <div class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                                        @if($enrollment->user->getProfilePictureUrl())
                                            <img src="{{ $enrollment->user->getProfilePictureUrl() }}" alt="{{ $enrollment->user->name }}" class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-medium">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $enrollment->user->name }}</span>
                                        @if($enrollment->user->country_code)
                                            <span class="text-xs text-gray-500 ml-1">{{ $enrollment->user->getCountryFlag() }}</span>
                                        @endif
                                    </div>
                                @endforeach
                                @if($course->enrollments->count() > 20)
                                    <div class="text-sm text-gray-500 dark:text-gray-400 px-3 py-1">
                                        +{{ $course->enrollments->count() - 20 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                <div x-show="activeTab === 'reviews'">
                    @if($course->reviews->count() > 0)
                        <!-- Rating Summary -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-8">
                            <div class="flex items-center space-x-8">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ number_format($course->rating, 1) }}</div>
                                    <div class="flex items-center justify-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $course->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $course->reviews->count() }} reviews</div>
                                </div>
                                <div class="flex-1">
                                    @for($rating = 5; $rating >= 1; $rating--)
                                        @php
                                            $count = $course->reviews->where('rating', $rating)->count();
                                            $percentage = $course->reviews->count() > 0 ? ($count / $course->reviews->count()) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm text-gray-600 dark:text-gray-400 w-8">{{ $rating }}</span>
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 w-8">{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        
                        <!-- Individual Reviews -->
                        <div class="space-y-6">
                            @foreach($course->reviews as $review)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0">
                                    <div class="flex items-start space-x-4">
                                        @if($review->user->getProfilePictureUrl())
                                            <img src="{{ $review->user->getProfilePictureUrl() }}" alt="{{ $review->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">{{ substr($review->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</span>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($review->comment)
                                                <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No reviews yet</h3>
                            <p class="text-gray-600 dark:text-gray-400">Be the first to review this course!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection