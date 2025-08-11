@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Categories</h1>
                <p class="text-gray-600 dark:text-gray-400">Organize courses into categories</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Add New Category
            </a>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}">
                            <span class="text-white font-bold text-lg">{{ substr($category->name, 0, 1) }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($category->is_active)
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            @else
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            @endif
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $category->description }}</p>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <span>{{ $category->courses_count }} courses</span>
                        <span>{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                            Edit
                        </a>
                        @if($category->courses_count == 0)
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                                        onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection