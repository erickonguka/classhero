@extends('layouts.teacher')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-gray-600 dark:text-gray-400">Manage and organize your courses</p>
        <a href="{{ route('teacher.courses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            Create New Course
        </a>
    </div>

    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search courses..." 
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="category_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
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
                            <span class="bg-{{ $course->category->color ?? 'blue' }}-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $course->category->name }}
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 z-10">
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
                                {{ $course->lessons->count() }} lessons • {{ $course->enrolled_count }} students
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $course->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        
                        <div class="flex space-x-1">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors">
                                Manage
                            </a>
                            <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors">
                                Preview
                            </a>
                            <button onclick="confirmDeleteCourse('{{ $course->slug }}', '{{ addslashes($course->title) }}')" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->appends(request()->query())->links() }}
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

<script>
function confirmDeleteCourse(courseSlug, courseTitle) {
    Swal.fire({
        title: 'Delete Course',
        text: `Are you sure you want to delete "${courseTitle}"? This action cannot be undone and will delete all lessons, quizzes, and student progress.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCourse(courseSlug);
        }
    });
}

function deleteCourse(courseSlug) {
    fetch(`/teacher/courses/${courseSlug}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Deleted!',
                text: data.message || 'Course deleted successfully!',
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to delete course');
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: error.message,
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    });
}
</script>