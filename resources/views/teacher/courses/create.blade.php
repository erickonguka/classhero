@extends('layouts.teacher')

@section('title', 'Create Course')
@section('page-title', 'Create New Course')

@section('content')
<div class="p-6" x-data="courseForm">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('teacher.courses.index') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                ← Back to Courses
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <form x-on:submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6" data-ajax="true">
                @csrf

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Title *</label>
                        <input type="text" id="title" name="title" x-model="formData.title" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               :class="{ 'border-red-500': errors.title }">
                        <p x-show="errors.title" class="mt-1 text-sm text-red-600" x-text="errors.title"></p>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                        <select id="category_id" name="category_id" x-model="formData.category_id" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': errors.category_id }">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p x-show="errors.category_id" class="mt-1 text-sm text-red-600" x-text="errors.category_id"></p>
                    </div>
                </div>

                <div>
                    <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Short Description *</label>
                    <textarea id="short_description" name="short_description" rows="3" x-model="formData.short_description" required
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              :class="{ 'border-red-500': errors.short_description }"></textarea>
                    <p x-show="errors.short_description" class="mt-1 text-sm text-red-600" x-text="errors.short_description"></p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Description *</label>
                    <textarea id="description" name="description" rows="6" x-model="formData.description" required
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              :class="{ 'border-red-500': errors.description }"></textarea>
                    <p x-show="errors.description" class="mt-1 text-sm text-red-600" x-text="errors.description"></p>
                </div>

                <!-- Course Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level *</label>
                        <select id="difficulty" name="difficulty" x-model="formData.difficulty" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': errors.difficulty }">
                            <option value="beginner" {{ old('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                        <p x-show="errors.difficulty" class="mt-1 text-sm text-red-600" x-text="errors.difficulty"></p>
                    </div>

                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (Hours) *</label>
                        <input type="number" id="duration_hours" name="duration_hours" x-model.number="formData.duration_hours" min="1"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               :class="{ 'border-red-500': errors.duration_hours }">
                        <p x-show="errors.duration_hours" class="mt-1 text-sm text-red-600" x-text="errors.duration_hours"></p>
                    </div>

                    <!-- Pricing temporarily disabled -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Type</label>
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
                            <p class="text-sm text-blue-800 dark:text-blue-200">All courses are currently free. Paid courses coming soon!</p>
                            <input type="hidden" name="is_free" value="1">
                        </div>
                    </div>
                </div>

                <!-- Pricing section commented out
                <div id="price-field" x-show="formData.is_free == '0'" x-cloak>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price ($)</label>
                    <input type="number" id="price" name="price" x-model.number="formData.price" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           :class="{ 'border-red-500': errors.price }">
                    <p x-show="errors.price" class="mt-1 text-sm text-red-600" x-text="errors.price"></p>
                </div>
                -->

                <!-- Course Thumbnail -->
                <div class="mb-6">
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Thumbnail (Optional)</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" x-on:change="previewThumbnail($event)"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           :class="{ 'border-red-500': errors.thumbnail }">
                    <p x-show="errors.thumbnail" class="mt-1 text-sm text-red-600" x-text="errors.thumbnail"></p>
                    <div id="thumbnail-preview" class="mt-2" x-show="previewImage" x-cloak>
                        <img id="preview-image" :src="previewImage" alt="Thumbnail preview" class="w-48 h-32 object-cover rounded-lg">
                    </div>
                </div>

                <!-- Learning Outcomes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">What You'll Learn *</label>
                    <div id="learning-outcomes">
                        <template x-for="(outcome, index) in formData.what_you_learn" :key="index">
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" x-model="formData.what_you_learn[index]"
                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                       placeholder="Learning outcome">
                                <button type="button" x-on:click="removeField('what_you_learn', index)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" x-on:click="addField('what_you_learn')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Add Learning Outcome
                    </button>
                    <p x-show="errors.what_you_learn" class="mt-1 text-sm text-red-600" x-text="errors.what_you_learn"></p>
                </div>

                <!-- Requirements -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requirements *</label>
                    <div id="requirements">
                        <template x-for="(requirement, index) in formData.requirements" :key="index">
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" x-model="formData.requirements[index]"
                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                       :class="{ 'border-red-500': errors.requirements }"
                                       placeholder="Course requirement">
                                <button type="button" x-on:click="removeField('requirements', index)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" x-on:click="addField('requirements')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Add Requirement
                    </button>
                    <p x-show="errors.requirements" class="mt-1 text-sm text-red-600" x-text="errors.requirements"></p>
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags *</label>
                    <input type="text" id="tags_input" name="tags_input" x-model="tagsInput" 
                           @input="updateTagsFromInput"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           :class="{ 'border-red-500': errors.tags }"
                           placeholder="Enter tags separated by commas (e.g., JavaScript, Web Development, Programming)">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Separate multiple tags with commas</p>
                    <div x-show="formData.tags.length > 0" class="flex flex-wrap gap-2 mt-2">
                        <template x-for="(tag, index) in formData.tags" :key="index">
                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm flex items-center">
                                <span x-text="tag"></span>
                                <button type="button" @click="removeTag(index)" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        </template>
                    </div>
                    <p x-show="errors.tags" class="mt-1 text-sm text-red-600" x-text="errors.tags"></p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('teacher.courses.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <x-spinning-button type="submit" x-bind:isLoading="isLoading">
                        Create Course
                    </x-spinning-button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set up route data for JavaScript
window.courseRoutes = {
    store: '{{ route('teacher.courses.store') }}'
};

// Set up old form data for JavaScript
window.oldFormData = {
    title: '{{ old('title') }}',
    category_id: '{{ old('category_id') }}',
    short_description: '{{ old('short_description') }}',
    description: '{{ old('description') }}',
    difficulty: '{{ old('difficulty', 'beginner') }}',
    duration_hours: '{{ old('duration_hours') }}',
    is_free: '{{ old('is_free', '1') }}',
    price: '{{ old('price') }}'
};
</script>
<script src="{{ asset('js/global-ajax.js') }}"></script>
<script src="{{ asset('js/teacher-courses.js') }}"></script>
@endsection