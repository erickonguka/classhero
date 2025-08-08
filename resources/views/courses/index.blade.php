@extends('layouts.app')

@section('title', 'All Courses')

@section('content')
<div class="bg-white dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">All Courses</h1>
            <p class="text-gray-600 dark:text-gray-400">Discover and learn from our comprehensive course library</p>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('courses.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search courses..." 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="category" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty Filter -->
                <div>
                    <select name="difficulty" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <select name="date_filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ request('date_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div>
                    <select name="sort" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Courses Grid -->
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($courses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative">
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
                            <div class="absolute top-4 left-4">
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
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
                                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">Completed</span>
                                            @else
                                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">{{ $enrollment->progress_percentage }}%</span>
                                            @endif
                                        </div>
                                    @elseif($course->is_free)
                                        <div class="absolute top-4 right-4">
                                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">Free</span>
                                        </div>
                                    @endif
                                @endif
                            @else
                                @if($course->is_free)
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">Free</span>
                                    </div>
                                @endif
                            @endauth
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">{{ Str::limit($course->short_description, 100) }}</p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    @if($course->teacher->getProfilePictureUrl())
                                        <img src="{{ $course->teacher->getProfilePictureUrl() }}" alt="{{ $course->teacher->name }}" class="w-6 h-6 rounded-full object-cover">
                                    @else
                                        <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-medium">{{ substr($course->teacher->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $course->teacher->name }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($course->rating, 1) }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $course->enrolled_count }} students • {{ $course->created_at->format('M d, Y') }}
                                </div>
                                @if(!$course->is_free)
                                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
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
                            
                            <a href="{{ route('courses.show', $course->slug) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center block">
                                View Course
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $courses->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No courses found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter criteria.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let timeout;
    
    // Reactive filtering
    $('input[name="search"], select[name="category"], select[name="difficulty"], select[name="date_filter"], select[name="sort"]').on('input change', function() {
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
            date_filter: $('select[name="date_filter"]').val(),
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