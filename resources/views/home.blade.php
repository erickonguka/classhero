@extends('layouts.app')

@section('title', 'Learn Anything, Anytime')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Learn Anything,<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">Anytime</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Join thousands of learners in our interactive e-learning platform. Master new skills with expert-led courses, quizzes, and hands-on projects.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('courses.index') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg">
                    Explore Courses
                </a>
                @guest
                    <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition-colors">
                        Start Learning Free
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ number_format($stats['total_courses']) }}</div>
                <div class="text-gray-600 dark:text-gray-400">Courses</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-green-600 dark:text-green-400 mb-2">{{ number_format($stats['total_students']) }}</div>
                <div class="text-gray-600 dark:text-gray-400">Students</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ number_format($stats['total_teachers']) }}</div>
                <div class="text-gray-600 dark:text-gray-400">Teachers</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-orange-600 dark:text-orange-400 mb-2">{{ number_format($stats['total_enrollments']) }}</div>
                <div class="text-gray-600 dark:text-gray-400">Enrollments</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="py-16 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Featured Courses</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">Discover our most popular and highly-rated courses</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredCourses as $course)
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                        @if($course->getFirstMediaUrl())
                            <img src="{{ $course->getFirstMediaUrl() }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
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
                        <div class="absolute top-4 left-4">
                            <span class="bg-{{ $course->category->color ?? 'blue' }}-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $course->category->name }}
                            </span>
                        </div>
                        @if($course->is_free)
                            <div class="absolute top-4 right-4">
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">Free</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ $course->short_description }}</p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr($course->teacher->name, 0, 1) }}</span>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $course->teacher->name }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($course->rating, 1) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $course->enrolled_count }} students
                                </div>
                                @if($course->is_free)
                                    <div class="text-lg font-bold text-green-600">Free</div>
                                @else
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($course->price, 2) }}</div>
                                @endif
                            </div>
                            <a href="{{ route('courses.show', $course->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('courses.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                View All Courses
            </a>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Browse by Category</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">Find courses in your area of interest</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('courses.index', ['category' => $category->slug]) }}" class="group">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all duration-300 group-hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ substr($category->name, 0, 1) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $category->courses_count }} courses</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Start Learning?</h2>
        <p class="text-xl mb-8 text-blue-100">Join our community of learners and start your journey today</p>
        @guest
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
                    Sign Up Free
                </a>
                <a href="{{ route('courses.index') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition-colors">
                    Browse Courses
                </a>
            </div>
        @else
            <a href="{{ route('courses.index') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
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