@extends('layouts.teacher')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('teacher.courses.show', $course) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                ← Back to Course
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">

        <form data-ajax data-success-message="Course updated successfully!" data-error-message="Failed to update course" action="{{ route('teacher.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Short Description *</label>
                <textarea id="short_description" name="short_description" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('short_description', $course->short_description) }}</textarea>
                @error('short_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Description *</label>
                <textarea id="description" name="description" rows="6" required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Settings -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level *</label>
                    <select id="difficulty" name="difficulty" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="beginner" {{ old('difficulty', $course->difficulty) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ old('difficulty', $course->difficulty) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ old('difficulty', $course->difficulty) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('difficulty')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (Hours) *</label>
                    <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('duration_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
            <div id="price-field" class="{{ old('is_free', $course->is_free) ? 'hidden' : '' }}">
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price ($)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $course->price) }}" step="0.01" min="0"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            -->

            <!-- Course Thumbnail -->
            <div class="mb-6">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewThumbnail(this)"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                @if($course->getFirstMediaUrl('thumbnails'))
                    <div class="mt-2">
                        <img src="{{ $course->getFirstMediaUrl('thumbnails') }}" alt="Current thumbnail" class="w-48 h-32 object-cover rounded-lg">
                    </div>
                @endif
                <div id="thumbnail-preview" class="mt-2 hidden">
                    <img id="preview-image" src="" alt="Thumbnail preview" class="w-48 h-32 object-cover rounded-lg">
                </div>
            </div>

            <!-- Learning Outcomes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">What You'll Learn *</label>
                <div id="learning-outcomes">
                    @if(old('what_you_learn', $course->what_you_learn))
                        @foreach(old('what_you_learn', $course->what_you_learn) as $index => $outcome)
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" name="what_you_learn[]" value="{{ $outcome }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                       placeholder="Learning outcome">
                                <button type="button" onclick="removeField(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="text" name="what_you_learn[]"
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="Learning outcome">
                            <button type="button" onclick="removeField(this)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addLearningOutcome()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    + Add Learning Outcome
                </button>
            </div>

            <!-- Requirements -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requirements *</label>
                <div id="requirements">
                    @if(old('requirements', $course->requirements))
                        @foreach(old('requirements', $course->requirements) as $index => $requirement)
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" name="requirements[]" value="{{ $requirement }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                       placeholder="Course requirement">
                                <button type="button" onclick="removeField(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="text" name="requirements[]"
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="Course requirement">
                            <button type="button" onclick="removeField(this)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addRequirement()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    + Add Requirement
                </button>
            </div>

            <!-- Tags -->
            <div x-data="{ 
                tagsInput: '{{ old('tags_input', is_array($course->tags) ? implode(', ', $course->tags) : '') }}',
                tags: {{ json_encode(old('tags', $course->tags ?? [])) }},
                updateTagsFromInput() {
                    if (this.tagsInput) {
                        this.tags = this.tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag);
                    } else {
                        this.tags = [];
                    }
                },
                removeTag(index) {
                    this.tags.splice(index, 1);
                    this.tagsInput = this.tags.join(', ');
                }
            }">
                <label for="tags_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags *</label>
                <input type="text" id="tags_input" name="tags_input" x-model="tagsInput" 
                       @input="updateTagsFromInput"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                       placeholder="Enter tags separated by commas (e.g., JavaScript, Web Development, Programming)">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Separate multiple tags with commas</p>
                <div x-show="tags.length > 0" class="flex flex-wrap gap-2 mt-2">
                    <template x-for="(tag, index) in tags" :key="index">
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
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('teacher.courses.show', $course) }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <x-spinning-button type="submit">
                    Update Course
                </x-spinning-button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
// Toggle price field based on course type
document.querySelectorAll('input[name="is_free"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const priceField = document.getElementById('price-field');
        if (this.value === '1') {
            priceField.classList.add('hidden');
        } else {
            priceField.classList.remove('hidden');
        }
    });
});

function addLearningOutcome() {
    const container = document.getElementById('learning-outcomes');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="what_you_learn[]"
               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
               placeholder="Learning outcome">
        <button type="button" onclick="removeField(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function addRequirement() {
    const container = document.getElementById('requirements');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="requirements[]"
               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
               placeholder="Course requirement">
        <button type="button" onclick="removeField(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

// Tags are now handled as comma-separated input

function removeField(button) {
    button.parentElement.remove();
}

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
<script src="{{ asset('js/forms.js') }}"></script>
@endsection