@extends('layouts.app')

@section('title', 'All Courses')

@section('content')
<!-- Page Loader -->
<div id="page-loader" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600 dark:text-gray-400">Loading courses...</p>
    </div>
</div>

<div class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Learn Without Limits</h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Choose from thousands of online courses from expert instructors</p>
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" id="hero-search" placeholder="What do you want to learn?" 
                           class="w-full px-6 py-4 text-gray-900 rounded-full text-lg focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <button class="absolute right-2 top-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full transition-colors">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 dark:text-white font-medium">All Courses</li>
            </ol>
        </nav>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Courses</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $courses->total() }} courses found</span>
            </div>
            <form method="GET" action="{{ route('courses.index') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-center lg:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search courses, instructors, topics..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="min-w-0 flex-1 lg:flex-none lg:w-48">
                    <select name="category" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty Filter -->
                <div class="min-w-0 flex-1 lg:flex-none lg:w-40">
                    <select name="difficulty" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>

                <!-- Price Filter -->
                <div class="min-w-0 flex-1 lg:flex-none lg:w-32">
                    <select name="price_filter" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">All Prices</option>
                        <option value="free" {{ request('price_filter') == 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ request('price_filter') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="min-w-0 flex-1 lg:flex-none lg:w-40">
                    <select name="sort" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span>Apply Filters</span>
                </button>
            </form>
        </div>

        <!-- Courses Grid -->
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                @foreach($courses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 group">
                        <div class="relative overflow-hidden">
                            <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                                @if($course->getFirstMediaUrl('thumbnails'))
                                    <img src="{{ $course->getFirstMediaUrl('thumbnails') }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                        <div class="text-center text-white">
                                            <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <p class="text-sm opacity-75">{{ $course->category->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                            </div>
                            <div class="absolute top-4 left-4">
                                <span class="bg-white bg-opacity-90 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm">
                                    {{ $course->category->name }}
                                </span>
                            </div>
                            @auth
                                @if(auth()->user()->isLearner())
                                    @php
                                        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();
                                    @endphp
                                    @if($enrollment)
                                        <div class="absolute top-4 right-4">
                                            @if($enrollment->progress_percentage == 100)
                                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">✓ Completed</span>
                                            @else
                                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $enrollment->progress_percentage }}%</span>
                                            @endif
                                        </div>
                                    @elseif($course->is_free)
                                        <div class="absolute top-4 right-4">
                                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">FREE</span>
                                        </div>
                                    @endif
                                @endif
                            @else
                                @if($course->is_free)
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">FREE</span>
                                    </div>
                                @endif
                            @endauth
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    @if($course->teacher->getProfilePictureUrl())
                                        <img src="{{ $course->teacher->getProfilePictureUrl() }}" alt="{{ $course->teacher->name }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">{{ substr($course->teacher->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $course->teacher->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->teacher->getTeacherRank() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($course->rating, 1) }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $course->reviews->count() }})</span>
                                </div>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ $course->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm line-clamp-2">{{ $course->short_description }}</p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <span>{{ $course->enrolled_count }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $course->duration_hours }}h</span>
                                    </span>
                                    <span class="capitalize px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">{{ $course->difficulty }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                @if($course->is_free)
                                    <div class="text-2xl font-bold text-green-600">FREE</div>
                                @else
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
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
                                <a href="{{ route('courses.show', $course->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition-all duration-300 hover:shadow-lg">
                                    View Course
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-12">
                {{ $courses->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No courses found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search or filter criteria to find what you're looking for.</p>
                <a href="{{ route('courses.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let timeout;
    
    // Hero search functionality
    $('#hero-search').on('keypress', function(e) {
        if (e.which === 13) {
            const query = $(this).val();
            if (query.trim()) {
                window.location.href = '{{ route("courses.index") }}?search=' + encodeURIComponent(query);
            }
        }
    });
    
    $('.hero-search-btn, button:contains("Search")').on('click', function() {
        const query = $('#hero-search').val();
        if (query.trim()) {
            window.location.href = '{{ route("courses.index") }}?search=' + encodeURIComponent(query);
        }
    });
    
    // Reactive filtering
    $('input[name="search"], select[name="category"], select[name="difficulty"], select[name="price_filter"], select[name="sort"]').on('input change', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            filterCourses();
        }, 500);
    });
    
    function filterCourses() {
        const formData = {
            search: $('input[name="search"]').val(),
            category: $('select[name="category"]').val(),
            difficulty: $('select[name="difficulty"]').val(),
            price_filter: $('select[name="price_filter"]').val(),
            sort: $('select[name="sort"]').val()
        };
        
        $.get('{{ route("courses.index") }}', formData, function(data) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const newContent = doc.querySelector('.grid');
            const newPagination = doc.querySelector('.flex.justify-center');
            
            if (newContent) {
                $('.grid').replaceWith(newContent);
            }
            if (newPagination) {
                $('.flex.justify-center').replaceWith(newPagination);
            }
        });
    }
});
</script>
@endpush