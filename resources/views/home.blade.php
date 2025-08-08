@extends('layouts.app')

@section('title', 'Learn Anything, Anytime')

@section('content')
<!-- Page Loader -->
<div id="page-loader" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600 dark:text-gray-400">Loading...</p>
    </div>
</div>
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-slate-800 via-blue-900 to-indigo-900 text-white overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-64 h-64 border-2 border-white rounded-full"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 border-2 border-white rounded-full"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 mb-6">
                <svg class="w-5 h-5 mr-2 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-sm font-medium">World-Class Learning</span>
            </div>
            
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Learn Anything,<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Anytime</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-slate-200 max-w-3xl mx-auto leading-relaxed">
                Empower your future with our interactive e-learning platform. Master skills through expert-led courses, quizzes, and hands-on projects.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ route('courses.index') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Explore Courses
                </a>
                @guest
                    <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-slate-900 transition-all duration-300">
                        Start Learning Free
                    </a>
                @endguest
            </div>
            
            <!-- Trust Indicators -->
            <div class="flex flex-wrap justify-center items-center gap-6 text-slate-300">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm">Expert Instructors</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm">Certified Courses</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm">Lifetime Access</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-slate-50 dark:bg-gray-900 border-b border-slate-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800 dark:text-white mb-2">Our Learning Community</h2>
            <p class="text-lg text-slate-600 dark:text-gray-400">Join thousands of learners worldwide</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-slate-200 dark:border-gray-700 hover:shadow-md transition-shadow stats-number" data-count="{{ $stats['total_courses'] }}">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ number_format($stats['total_courses']) }}</div>
                <div class="text-slate-600 dark:text-gray-400 font-medium">Courses</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-slate-200 dark:border-gray-700 hover:shadow-md transition-shadow stats-number" data-count="{{ $stats['total_students'] }}">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-green-600 dark:text-green-400 mb-2">{{ number_format($stats['total_students']) }}</div>
                <div class="text-slate-600 dark:text-gray-400 font-medium">Students</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-slate-200 dark:border-gray-700 hover:shadow-md transition-shadow stats-number" data-count="{{ $stats['total_teachers'] }}">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ number_format($stats['total_teachers']) }}</div>
                <div class="text-slate-600 dark:text-gray-400 font-medium">Teachers</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-slate-200 dark:border-gray-700 hover:shadow-md transition-shadow stats-number" data-count="{{ $stats['total_enrollments'] }}">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-orange-600 dark:text-orange-400 mb-2">{{ number_format($stats['total_enrollments']) }}</div>
                <div class="text-slate-600 dark:text-gray-400 font-medium">Enrollments</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 rounded-full px-4 py-2 mb-4">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-sm font-semibold">Featured Courses</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Discover Top Courses</h2>
            <p class="text-xl text-slate-600 dark:text-gray-400 max-w-2xl mx-auto">Explore our most popular and highly-rated courses, designed by experts</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredCourses as $course)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 overflow-hidden group">
                    <div class="relative h-48">
                        @if($course->getFirstMediaUrl('thumbnails'))
                            <img src="{{ $course->getFirstMediaUrl('thumbnails') }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900 dark:to-indigo-900">
                                <div class="text-center text-slate-600 dark:text-slate-300">
                                    <svg class="w-16 h-16 mx-auto mb-2 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <p class="text-sm font-medium">{{ $course->category->name }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-slate-900/80 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-medium">
                                {{ $course->category->name }}
                            </span>
                        </div>
                        @if($course->is_free)
                            <div class="absolute top-4 right-4">
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg">Free</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $course->title }}</h3>
                        <p class="text-slate-600 dark:text-gray-400 mb-4 line-clamp-3 text-sm">{{ $course->short_description }}</p>
                        
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100 dark:border-gray-700">
                            <div class="flex items-center space-x-2">
                                @if($course->teacher->getProfilePictureUrl())
                                    <img src="{{ $course->teacher->getProfilePictureUrl() }}" alt="{{ $course->teacher->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-gray-600">
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center border-2 border-slate-200 dark:border-gray-600">
                                        <span class="text-white text-sm font-medium">{{ substr($course->teacher->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="text-sm text-slate-600 dark:text-gray-400">{{ $course->teacher->name }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="text-sm text-slate-600 dark:text-gray-400">{{ number_format($course->rating, 1) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <div class="text-sm text-slate-500 dark:text-gray-400">
                                    {{ $course->enrolled_count }} students • {{ $course->created_at->format('M d, Y') }}
                                </div>
                                @if($course->is_free)
                                    <div class="text-lg font-bold text-green-600">Free</div>
                                @else
                                    <div class="text-lg font-bold text-slate-900 dark:text-white">
                                        @auth
                                            {{ auth()->user()->getCurrencySymbol() }}{{ number_format(\App\Services\CurrencyService::convert($course->price, 'USD', auth()->user()->currency), 2) }}
                                        @else
                                            @php
                                                $convertedPrice = \App\Services\CurrencyService::convert($course->price, 'USD', $userCurrency ?? 'USD');
                                                $symbol = \App\Services\CurrencyService::getSymbol($userCurrency ?? 'USD');
                                            @endphp
                                            {{ $symbol }}{{ number_format($convertedPrice, 2) }}
                                        @endauth
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('courses.show', $course->slug) }}" class="bg-slate-900 dark:bg-slate-100 hover:bg-blue-600 dark:hover:bg-blue-600 text-white dark:text-slate-900 dark:hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('courses.index') }}" class="bg-slate-900 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                View All Courses
            </a>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-16 bg-slate-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full px-4 py-2 mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="text-sm font-semibold">Categories</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Browse by Category</h2>
            <p class="text-xl text-slate-600 dark:text-gray-400 max-w-2xl mx-auto">Find courses tailored to your interests</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('courses.index', ['category' => $category->slug]) }}" class="group">
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 text-center shadow-sm border border-slate-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 group-hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:from-blue-100 group-hover:to-indigo-200 dark:group-hover:from-blue-800 dark:group-hover:to-indigo-800 transition-all duration-300">
                            <span class="text-slate-700 dark:text-slate-300 text-2xl font-bold group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ substr($category->name, 0, 1) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $category->name }}</h3>
                        <p class="text-sm text-slate-500 dark:text-gray-400">{{ $category->courses_count }} courses</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Begin Your Journey?</h2>
        <p class="text-xl mb-8 text-blue-100">Join our global community of learners and start mastering new skills today</p>
        @guest
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Sign Up Free
                </a>
                <a href="{{ route('courses.index') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-slate-900 transition-all duration-300">
                    Browse Courses
                </a>
            </div>
        @else
            <a href="{{ route('courses.index') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Continue Learning
            </a>
        @endguest
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Animate stats on scroll
    function animateStats() {
        $('.stats-number').each(function() {
            var $this = $(this);
            var countTo = $this.attr('data-count');
            
            $({ countNum: $this.text()}).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });
    }

    // Trigger animation when stats section is visible
    $(window).scroll(function() {
        var statsSection = $('.stats-section');
        if (statsSection.length) {
            var statsTop = statsSection.offset().top;
            var statsBottom = statsTop + statsSection.outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (statsBottom > viewportTop && statsTop < viewportBottom) {
                animateStats();
                $(window).off('scroll'); // Remove scroll listener after animation
            }
        }
    });
});
</script>
@endpush