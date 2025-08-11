@php
    // Generate a new UUID for this artifact since it's a modified version but not directly updating a previous one
@endphp

@extends('layouts.teacher')

@section('title', 'Create Lesson')
@section('page-title', 'Create Lesson')

@section('content')
<div class="p-4 sm:p-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('teacher.courses.show', $course) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to Course
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6">
            <form data-ajax data-success-message="Lesson created successfully!" data-error-message="Failed to create lesson" id="lesson-form" action="{{ route('teacher.courses.lessons.store', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Progress Indicator -->
                <div class="mb-6 sm:mb-8 progress-indicator">
                    <div class="flex flex-nowrap overflow-x-auto gap-2 sm:gap-4 pb-2">
                        <button type="button" data-step="basic" class="step-tab flex-shrink-0 px-3 py-2 text-xs sm:text-sm font-medium rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">1. Basic Info</button>
                        <button type="button" data-step="content" class="step-tab flex-shrink-0 px-3 py-2 text-xs sm:text-sm font-medium rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">2. Content</button>
                        <button type="button" data-step="media" class="step-tab flex-shrink-0 px-3 py-2 text-xs sm:text-sm font-medium rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">3. Media</button>
                        <button type="button" data-step="settings" class="step-tab flex-shrink-0 px-3 py-2 text-xs sm:text-sm font-medium rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">4. Settings</button>
                    </div>
                </div>

                <!-- Step 1: Basic Information -->
                <div id="step-basic" class="step-content">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lesson Title *</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                   class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter lesson title">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)</label>
                            <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1"
                                   class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="e.g., 30">
                            @error('duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4 sm:mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Describe what this lesson covers">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" data-next="content" class="next-step px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-xs sm:text-sm">Next: Content</button>
                    </div>
                </div>

                <!-- Step 2: Lesson Content -->
                <div id="step-content" class="step-content hidden">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Lesson Content</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                        <textarea id="content" name="content">{{ old('content') }}</textarea>
                    </div>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-4">
                        <button type="button" data-prev="basic" class="prev-step px-4 sm:px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-xs sm:text-sm">Back</button>
                        <button type="button" data-next="media" class="next-step px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-xs sm:text-sm">Next: Media</button>
                    </div>
                </div>

                <!-- Step 3: Media Attachments -->
                <div id="step-media" class="step-content hidden">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">📎 Media Attachments</h2>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-4 sm:p-6 border border-blue-200 dark:border-gray-600 mb-4">
                        <!-- Quick Actions -->
                        <div class="flex flex-wrap gap-2 sm:gap-3 mb-4 sm:mb-6">
                            <button type="button" id="upload-files-btn" class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 text-xs sm:text-sm">
                                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <span>Upload Files</span>
                            </button>
                            <button type="button" id="record-video-btn" class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 text-xs sm:text-sm">
                                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>Record Video</span>
                            </button>
                            <button type="button" id="record-audio-btn" class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 text-xs sm:text-sm">
                                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                <span>Record Audio</span>
                            </button>
                            <button type="button" id="add-youtube-btn" class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 transform hover:scale-105 text-xs sm:text-sm">
                                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                <span>YouTube</span>
                            </button>
                            <button type="button" id="add-link-btn" class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 text-xs sm:text-sm">
                                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Add Link</span>
                            </button>
                        </div>
                        <!-- Hidden File Inputs -->
                        <input type="file" id="media-files" name="media_files[]" multiple accept="*" class="hidden">
                        <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}" class="hidden">
                        <input type="url" id="external_url" name="external_url" value="{{ old('external_url') }}" class="hidden">
                        <input type="hidden" id="media_data" name="media_data" value="{{ old('media_data', json_encode([])) }}">
                        <!-- Dropzone -->
                        <div id="dropzone" class="border-2 border-dashed border-blue-300 dark:border-gray-500 rounded-lg p-6 sm:p-8 text-center hover:border-blue-500 hover:bg-blue-100 dark:hover:bg-gray-600 transition-all duration-300 cursor-pointer">
                            <svg class="w-10 sm:w-12 h-10 sm:h-12 mx-auto mb-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-300 font-medium text-sm sm:text-base">Drop files here or click to browse</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">Videos, Audio, Images, PDFs, Documents (max 100MB per file)</p>
                        </div>
                    </div>
                    <!-- Media Items Container -->
                    <div id="media-items" class="space-y-3"></div>
                    <!-- Recording Interface -->
                    <div id="recording-interface" class="hidden mt-4 p-4 sm:p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl border">
                        <video id="video-preview" class="w-full max-w-md mx-auto rounded-lg shadow-lg hidden" autoplay muted></video>
                        <div id="audio-visualizer" class="hidden text-center py-6 sm:py-8">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-4 sm:p-6 text-white">
                                <div class="flex justify-center space-x-1 mb-4">
                                    <div class="w-2 h-8 bg-white bg-opacity-60 rounded animate-pulse" style="animation-delay: 0ms"></div>
                                    <div class="w-2 h-12 bg-white bg-opacity-60 rounded animate-pulse" style="animation-delay: 100ms"></div>
                                    <div class="w-2 h-6 bg-white bg-opacity-60 rounded animate-pulse" style="animation-delay: 200ms"></div>
                                    <div class="w-2 h-10 bg-white bg-opacity-60 rounded animate-pulse" style="animation-delay: 300ms"></div>
                                    <div class="w-2 h-4 bg-white bg-opacity-60 rounded animate-pulse" style="animation-delay: 400ms"></div>
                                </div>
                                <div class="text-xl sm:text-2xl font-mono" id="recording-time">00:00</div>
                            </div>
                        </div>
                        <div class="flex justify-center space-x-4 mt-4">
                            <button type="button" id="start-recording" class="flex items-center space-x-2 px-4 sm:px-6 py-2 sm:py-3 bg-red-600 hover:bg-red-700 text-white rounded-full transition-all duration-200 text-xs sm:text-sm">
                                <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                <span>Start Recording</span>
                            </button>
                            <button type="button" id="stop-recording" class="hidden flex items-center space-x-2 px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-full transition-all duration-200 text-xs sm:text-sm">
                                <div class="w-3 h-3 bg-white"></div>
                                <span>Stop Recording</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" data-prev="content" class="prev-step px-4 sm:px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-xs sm:text-sm">Back</button>
                        <button type="button" data-next="settings" class="next-step px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-xs sm:text-sm">Next: Settings</button>
                    </div>
                </div>

                <!-- Step 4: Lesson Settings -->
                <div id="step-settings" class="step-content hidden">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Lesson Settings</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="flex items-center">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires enrollment</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Completion Requirements</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_video_completion" value="1" {{ old('require_video_completion') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Require video completion</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_quiz_pass" value="1" {{ old('require_quiz_pass') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Require quiz pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_comment" value="1" {{ old('require_comment') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Require comment</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" data-prev="media" class="prev-step px-4 sm:px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-xs sm:text-sm">Back</button>
                        <x-spinning-button type="submit" id="submit-btn">
                            Create Lesson
                        </x-spinning-button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4 sm:mt-6 hidden">
                    <button type="submit" id="submit-btn" class="px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center">
                        <span id="submit-text">Create Lesson</span>
                        <svg id="submit-spinner" class="w-5 h-5 ml-2 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.progress-indicator {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.progress-indicator::-webkit-scrollbar {
    display: none;
}
.progress-indicator .flex {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
}
.step-tab {
    flex: 0 0 auto;
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
    white-space: nowrap;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    min-height: 44px;
    line-height: 1.5;
}
@media (min-width: 640px) {
    .progress-indicator .flex {
        gap: 1rem;
        padding-left: 0;
    }
    .step-tab {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
}
.step-tab.bg-blue-100,
.step-tab.dark\:bg-blue-900 {
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/forms.js') }}"></script>
<script>
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    if (typeof window.initTinyMCE === 'function') {
        window.initTinyMCE('#content');
    }

    // Step Navigation
    const steps = document.querySelectorAll('.step-content');
    const stepTabs = document.querySelectorAll('.step-tab');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');

    function showStep(stepId) {
        steps.forEach(step => step.classList.add('hidden'));
        document.getElementById(`step-${stepId}`).classList.remove('hidden');
        stepTabs.forEach(tab => {
            tab.classList.remove('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300');
            tab.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
        });
        const activeTab = document.querySelector(`.step-tab[data-step="${stepId}"]`);
        activeTab.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
        activeTab.classList.add('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300');
    }

    stepTabs.forEach(tab => {
        tab.addEventListener('click', () => showStep(tab.dataset.step));
    });

    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const nextStep = button.dataset.next;
            if (validateStep(button.closest('.step-content').id.replace('step-', ''))) {
                showStep(nextStep);
            }
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', () => showStep(button.dataset.prev));
    });

    // Form Validation
    function validateStep(step) {
        const inputs = document.querySelectorAll(`#step-${step} [required]`);
        let valid = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('border-red-500');
                toastr.error(`${input.previousElementSibling.textContent} is required`);
            } else {
                input.classList.remove('border-red-500');
            }
            if (input.type === 'number' && input.value <= 0) {
                valid = false;
                input.classList.add('border-red-500');
                toastr.error(`${input.previousElementSibling.textContent} must be a positive number`);
            }
        });
        return valid;
    }

    // Media Handling
    const dropzone = document.getElementById('dropzone');
    const mediaFiles = document.getElementById('media-files');
    const mediaItems = document.getElementById('media-items');
    let mediaCounter = 0;
    let attachedMedia = [];

    function getMediaType(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        const videoExts = ['mp4', 'webm', 'avi', 'mov', 'mkv'];
        const audioExts = ['mp3', 'wav', 'ogg', 'aac', 'm4a'];
        const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        const docExts = ['docx', 'doc'];

        if (videoExts.includes(ext)) return 'video';
        if (audioExts.includes(ext)) return 'audio';
        if (imageExts.includes(ext)) return 'image';
        if (ext === 'pdf') return 'pdf';
        if (docExts.includes(ext)) return 'document';
        return 'file';
    }

    function addMediaItem(file, type = null, url = null, title = null, description = null) {
        const mediaType = type || getMediaType(file);
        const itemId = 'media-' + (++mediaCounter);

        const mediaItem = document.createElement('div');
        mediaItem.className = 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4';
        mediaItem.id = itemId;
        mediaItem.draggable = true;

        const header = document.createElement('div');
        header.className = 'flex items-center justify-between mb-3';

        const leftContent = document.createElement('div');
        leftContent.className = 'flex items-center space-x-3';

        const icon = document.createElement('div');
        icon.className = `w-10 h-10 rounded-lg flex items-center justify-center ${getMediaColor(mediaType)}`;
        icon.innerHTML = getMediaIcon(mediaType);

        const info = document.createElement('div');
        const name = document.createElement('div');
        name.className = 'font-medium text-gray-900 dark:text-white';
        name.textContent = title || (file ? file.name : (type === 'youtube' ? 'YouTube Video' : 'External Link'));

        const details = document.createElement('div');
        details.className = 'text-sm text-gray-500';
        details.textContent = file ? `${(file.size / 1024 / 1024).toFixed(2)} MB • ${mediaType.toUpperCase()}` : url;

        info.appendChild(name);
        info.appendChild(details);
        leftContent.appendChild(icon);
        leftContent.appendChild(info);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'text-red-600 hover:text-red-800 transition-colors';
        removeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        removeBtn.onclick = () => removeMediaItem(itemId);

        header.appendChild(leftContent);
        header.appendChild(removeBtn);

        const fields = document.createElement('div');
        fields.className = 'space-y-3';

        const titleInput = document.createElement('input');
        titleInput.type = 'text';
        titleInput.placeholder = 'Media title...';
        titleInput.value = title || (file ? file.name : '');
        titleInput.className = 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white';
        titleInput.onchange = () => updateMediaData();

        const descInput = document.createElement('textarea');
        descInput.placeholder = 'Description (optional)...';
        descInput.value = description || '';
        descInput.rows = 2;
        descInput.className = 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white';
        descInput.onchange = () => updateMediaData();

        fields.appendChild(titleInput);
        fields.appendChild(descInput);

        mediaItem.appendChild(header);
        mediaItem.appendChild(fields);

        if (mediaType === 'image' && file) {
            const preview = document.createElement('div');
            preview.className = 'mt-3';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'max-w-xs rounded-lg shadow-lg';
            preview.appendChild(img);
            mediaItem.appendChild(preview);
        }

        mediaItems.appendChild(mediaItem);

        attachedMedia.push({
            id: itemId,
            file,
            type: mediaType,
            url,
            titleInput,
            descInput,
            order: attachedMedia.length
        });

        updateMediaData();
        toastr.success(`${file ? file.name : 'Media'} added successfully!`);

        mediaItem.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', itemId);
        });
        mediaItem.addEventListener('dragover', (e) => {
            e.preventDefault();
        });
        mediaItem.addEventListener('drop', (e) => {
            e.preventDefault();
            const draggedId = e.dataTransfer.getData('text/plain');
            const draggedItem = attachedMedia.find(item => item.id === draggedId);
            const targetItem = attachedMedia.find(item => item.id === mediaItem.id);
            if (draggedItem && targetItem) {
                const draggedIndex = attachedMedia.indexOf(draggedItem);
                const targetIndex = attachedMedia.indexOf(targetItem);
                attachedMedia.splice(draggedIndex, 1);
                attachedMedia.splice(targetIndex, 0, draggedItem);
                attachedMedia.forEach((item, index) => item.order = index);
                updateMediaData();
                renderMediaItems();
            }
        });
    }

    function renderMediaItems() {
        mediaItems.innerHTML = '';
        attachedMedia.forEach(item => {
            const file = item.file;
            const mediaType = item.type;
            const itemId = item.id;

            const mediaItem = document.createElement('div');
            mediaItem.className = 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4';
            mediaItem.id = itemId;
            mediaItem.draggable = true;

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between mb-3';

            const leftContent = document.createElement('div');
            leftContent.className = 'flex items-center space-x-3';

            const icon = document.createElement('div');
            icon.className = `w-10 h-10 rounded-lg flex items-center justify-center ${getMediaColor(mediaType)}`;
            icon.innerHTML = getMediaIcon(mediaType);

            const info = document.createElement('div');
            const name = document.createElement('div');
            name.className = 'font-medium text-gray-900 dark:text-white';
            name.textContent = item.titleInput.value;

            const details = document.createElement('div');
            details.className = 'text-sm text-gray-500';
            details.textContent = file ? `${(file.size / 1024 / 1024).toFixed(2)} MB • ${mediaType.toUpperCase()}` : item.url;

            info.appendChild(name);
            info.appendChild(details);
            leftContent.appendChild(icon);
            leftContent.appendChild(info);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-red-600 hover:text-red-800 transition-colors';
            removeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            removeBtn.onclick = () => removeMediaItem(itemId);

            header.appendChild(leftContent);
            header.appendChild(removeBtn);

            const fields = document.createElement('div');
            fields.className = 'space-y-3';
            fields.appendChild(item.titleInput);
            fields.appendChild(item.descInput);

            mediaItem.appendChild(header);
            mediaItem.appendChild(fields);

            if (mediaType === 'image' && file) {
                const preview = document.createElement('div');
                preview.className = 'mt-3';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'max-w-xs rounded-lg shadow-lg';
                preview.appendChild(img);
                mediaItem.appendChild(preview);
            }

            mediaItems.appendChild(mediaItem);

            mediaItem.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', itemId);
            });
            mediaItem.addEventListener('dragover', (e) => {
                e.preventDefault();
            });
            mediaItem.addEventListener('drop', (e) => {
                e.preventDefault();
                const draggedId = e.dataTransfer.getData('text/plain');
                const draggedItem = attachedMedia.find(item => item.id === draggedId);
                const targetItem = attachedMedia.find(item => item.id === mediaItem.id);
                if (draggedItem && targetItem) {
                    const draggedIndex = attachedMedia.indexOf(draggedItem);
                    const targetIndex = attachedMedia.indexOf(targetItem);
                    attachedMedia.splice(draggedIndex, 1);
                    attachedMedia.splice(targetIndex, 0, draggedItem);
                    attachedMedia.forEach((item, index) => item.order = index);
                    updateMediaData();
                    renderMediaItems();
                }
            });
        });
    }

    function removeMediaItem(itemId) {
        document.getElementById(itemId).remove();
        attachedMedia = attachedMedia.filter(item => item.id !== itemId);
        attachedMedia.forEach((item, index) => item.order = index);
        updateMediaData();
    }

    function updateMediaData() {
        const mediaData = attachedMedia.map(item => ({
            type: item.type,
            title: item.titleInput ? item.titleInput.value : '',
            description: item.descInput ? item.descInput.value : '',
            url: item.url || '',
            order: item.order
        }));
        document.getElementById('media_data').value = JSON.stringify(mediaData);
    }

    function getMediaColor(type) {
        const colors = {
            video: 'bg-red-100 text-red-600',
            audio: 'bg-purple-100 text-purple-600',
            image: 'bg-green-100 text-green-600',
            pdf: 'bg-red-100 text-red-600',
            document: 'bg-blue-100 text-blue-600',
            youtube: 'bg-red-100 text-red-600',
            link: 'bg-green-100 text-green-600'
        };
        return colors[type] || 'bg-gray-100 text-gray-600';
    }

    function getMediaIcon(type) {
        const icons = {
            video: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>',
            audio: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>',
            image: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
            pdf: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
            document: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
            youtube: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
            link: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>'
        };
        return icons[type] || icons.document;
    }

    document.getElementById('upload-files-btn').addEventListener('click', () => {
        mediaFiles.click();
    });

    dropzone.addEventListener('click', () => {
        mediaFiles.click();
    });

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-blue-500', 'bg-blue-100');
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-blue-500', 'bg-blue-100');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-blue-500', 'bg-blue-100');
        const files = Array.from(e.dataTransfer.files);
        files.forEach(file => {
            if (file.size > 100 * 1024 * 1024) {
                toastr.error(`${file.name} is too large. Maximum size is 100MB.`);
                return;
            }
            addMediaItem(file);
        });
    });

    mediaFiles.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (file.size > 100 * 1024 * 1024) {
                toastr.error(`${file.name} is too large. Maximum size is 100MB.`);
                return;
            }
            addMediaItem(file);
        });
    });

    document.getElementById('record-video-btn').addEventListener('click', async () => {
        await initRecording('video');
    });

    document.getElementById('record-audio-btn').addEventListener('click', async () => {
        await initRecording('audio');
    });

    document.getElementById('add-youtube-btn').addEventListener('click', () => {
        addYouTubeUrl();
    });

    document.getElementById('add-link-btn').addEventListener('click', () => {
        addExternalLink();
    });

    function addYouTubeUrl() {
        const url = prompt('Enter YouTube URL:');
        if (url && (url.includes('youtube.com') || url.includes('youtu.be'))) {
            document.getElementById('video_url').value = url;
            addMediaItem(null, 'youtube', url);
        } else if (url) {
            toastr.error('Please enter a valid YouTube URL');
        }
    }

    function addExternalLink() {
        const url = prompt('Enter external link URL:');
        if (url && url.startsWith('http')) {
            document.getElementById('external_url').value = url;
            addMediaItem(null, 'link', url);
        } else if (url) {
            toastr.error('Please enter a valid URL starting with http');
        }
    }

    // Recording Functions
    let mediaRecorder;
    let recordedChunks = [];
    let recordingTimer;
    let recordingSeconds = 0;

    async function initRecording(type) {
        try {
            const constraints = type === 'video' ? { video: true, audio: true } : { audio: true };
            const stream = await navigator.mediaDevices.getUserMedia(constraints);

            const recordingInterface = document.getElementById('recording-interface');
            recordingInterface.classList.remove('hidden');

            if (type === 'video') {
                const videoPreview = document.getElementById('video-preview');
                videoPreview.srcObject = stream;
                videoPreview.classList.remove('hidden');
                document.getElementById('audio-visualizer').classList.add('hidden');
            } else {
                document.getElementById('video-preview').classList.add('hidden');
                document.getElementById('audio-visualizer').classList.remove('hidden');
            }

            document.getElementById('start-recording').onclick = () => startRecording(stream, type);
            document.getElementById('stop-recording').onclick = () => stopRecording(type);
        } catch (err) {
            console.error('Media access error:', err);
            toastr.error(`${type === 'video' ? 'Camera' : 'Microphone'} access denied`);
        }
    }

    function startRecording(stream, type) {
        recordedChunks = [];
        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = event => {
            if (event.data.size > 0) recordedChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            const blob = new Blob(recordedChunks, { type: type === 'video' ? 'video/webm' : 'audio/webm' });
            if (blob.size > 100 * 1024 * 1024) {
                toastr.error('Recording is too large. Maximum size is 100MB.');
                stream.getTracks().forEach(track => track.stop());
                return;
            }
            const file = new File([blob], `recorded-${type}.webm`, { type: blob.type });
            addMediaItem(file, type);
            document.getElementById('recording-interface').classList.add('hidden');
            toastr.success(`${type.charAt(0).toUpperCase() + type.slice(1)} recorded successfully!`);
            stream.getTracks().forEach(track => track.stop());
        };

        mediaRecorder.start();
        document.getElementById('start-recording').classList.add('hidden');
        document.getElementById('stop-recording').classList.remove('hidden');
        startTimer();
    }

    function startTimer() {
        recordingSeconds = 0;
        recordingTimer = setInterval(() => {
            recordingSeconds++;
            const minutes = Math.floor(recordingSeconds / 60);
            const seconds = recordingSeconds % 60;
            const timeEl = document.getElementById('recording-time');
            if (timeEl) {
                timeEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }

    function stopTimer() {
        if (recordingTimer) {
            clearInterval(recordingTimer);
        }
    }

    function stopRecording(type) {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        document.getElementById('start-recording').classList.remove('hidden');
        document.getElementById('stop-recording').classList.add('hidden');
        stopTimer();
    }

    // AJAX Form Submission
    document.getElementById('lesson-form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!validateStep('basic') || !validateStep('content')) {
            toastr.error('Please complete all required fields');
            return;
        }

        Swal.fire({
            title: 'Create Lesson?',
            text: 'Are you sure you want to create this lesson?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create it!',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = e.target;
                const formData = new FormData(form);
                const submitBtn = document.getElementById('submit-btn');
                const submitText = document.getElementById('submit-text');
                const submitSpinner = document.getElementById('submit-spinner');

                submitBtn.disabled = true;
                submitText.textContent = 'Processing...';
                submitSpinner.classList.remove('hidden');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitText.textContent = 'Create Lesson';
                    submitSpinner.classList.add('hidden');

                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Lesson created successfully!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        toastr.error(data.message || 'An error occurred while creating the lesson.');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitText.textContent = 'Create Lesson';
                    submitSpinner.classList.add('hidden');
                    toastr.error('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            }
        });
    });
});
</script>
@endpush