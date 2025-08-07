@extends('layouts.app')

@section('title', 'Create New Course')

@section('content')
<div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create New Course</h1>
            <p class="text-gray-600 dark:text-gray-400">Build an engaging learning experience for your students</p>
        </div>

        <form action="{{ route('teacher.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="Enter course title" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select id="category_id" name="category_id" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level</label>
                        <select id="difficulty" name="difficulty" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Difficulty</option>
                            <option value="beginner" {{ old('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                        @error('difficulty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Short Description</label>
                        <textarea id="short_description" name="short_description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Brief description of your course (max 500 characters)" required>{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Description</label>
                        <textarea id="description" name="description" rows="6" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Detailed description of your course content and objectives" required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Duration -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Pricing & Duration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Type</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="is_free" value="1" {{ old('is_free', '1') == '1' ? 'checked' : '' }} 
                                       class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Free Course</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="is_free" value="0" {{ old('is_free') == '0' ? 'checked' : '' }} 
                                       class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Paid Course</span>
                            </label>
                        </div>
                    </div>

                    <div id="price-field" style="display: none;">
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price (USD)</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="0.00">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (Hours)</label>
                        <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours') }}" min="1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="Estimated course duration">
                        @error('duration_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Course Thumbnail -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Course Thumbnail</h2>
                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Course Image</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewThumbnail(this)"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <div id="thumbnail-preview" class="mt-4 hidden">
                        <img id="preview-image" src="" alt="Thumbnail preview" class="w-48 h-32 object-cover rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Course Sections -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Course Sections</h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="overview" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Overview</label>
                        <textarea id="overview" name="overview" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Detailed overview of the course content and objectives">{{ old('overview') }}</textarea>
                    </div>
                    
                    <div>
                        <label for="instructor_info" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructor Information</label>
                        <textarea id="instructor_info" name="instructor_info" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Information about yourself as the instructor">{{ old('instructor_info') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Learning Outcomes -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">What Students Will Learn</h2>
                
                <div id="learning-outcomes">
                    <div class="learning-outcome-item flex items-center space-x-2 mb-3">
                        <input type="text" name="what_you_learn[]" 
                               class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="What will students learn?">
                        <button type="button" class="remove-outcome text-red-600 hover:text-red-700 p-2" style="display: none;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" id="add-outcome" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    + Add Learning Outcome
                </button>
            </div>

            <!-- Requirements -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Course Requirements</h2>
                
                <div id="requirements">
                    <div class="requirement-item flex items-center space-x-2 mb-3">
                        <input type="text" name="requirements[]" 
                               class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="What do students need to know or have?">
                        <button type="button" class="remove-requirement text-red-600 hover:text-red-700 p-2" style="display: none;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" id="add-requirement" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    + Add Requirement
                </button>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('teacher.courses.index') }}" class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium">
                    Cancel
                </a>
                <div class="space-x-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Create Course
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide price field based on course type
    $('input[name="is_free"]').on('change', function() {
        if ($(this).val() == '0') {
            $('#price-field').show();
            $('#price').attr('required', true);
        } else {
            $('#price-field').hide();
            $('#price').attr('required', false);
        }
    });

    // Initialize price field visibility
    if ($('input[name="is_free"]:checked').val() == '0') {
        $('#price-field').show();
        $('#price').attr('required', true);
    }

    // Add learning outcome
    $('#add-outcome').on('click', function() {
        const newItem = `
            <div class="learning-outcome-item flex items-center space-x-2 mb-3">
                <input type="text" name="what_you_learn[]" 
                       class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                       placeholder="What will students learn?">
                <button type="button" class="remove-outcome text-red-600 hover:text-red-700 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
        $('#learning-outcomes').append(newItem);
        updateRemoveButtons();
    });

    // Add requirement
    $('#add-requirement').on('click', function() {
        const newItem = `
            <div class="requirement-item flex items-center space-x-2 mb-3">
                <input type="text" name="requirements[]" 
                       class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                       placeholder="What do students need to know or have?">
                <button type="button" class="remove-requirement text-red-600 hover:text-red-700 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
        $('#requirements').append(newItem);
        updateRemoveButtons();
    });

    // Remove outcome/requirement
    $(document).on('click', '.remove-outcome', function() {
        $(this).closest('.learning-outcome-item').remove();
        updateRemoveButtons();
    });

    $(document).on('click', '.remove-requirement', function() {
        $(this).closest('.requirement-item').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        $('.remove-outcome').toggle($('.learning-outcome-item').length > 1);
        $('.remove-requirement').toggle($('.requirement-item').length > 1);
    }

    updateRemoveButtons();
});

// Thumbnail preview function
function previewThumbnail(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('thumbnail-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush