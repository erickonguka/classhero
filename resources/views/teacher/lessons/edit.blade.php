@extends('layouts.teacher')

@section('title', 'Edit Lesson')
@section('page-title', 'Edit Lesson')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('teacher.courses.show', $lesson->course) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                ← Back to Course
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">

        <form action="{{ route('teacher.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lesson Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $lesson->title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lesson Type</label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="video" {{ old('type', $lesson->type) == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="text" {{ old('type', $lesson->type) == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="pdf" {{ old('type', $lesson->type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="audio" {{ old('type', $lesson->type) == 'audio' ? 'selected' : '' }}>Audio</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('description', $lesson->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order</label>
                    <input type="number" id="order" name="order" value="{{ old('order', $lesson->order) }}" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <!-- Video URL Field -->
            <div id="video-url-field" class="mb-6" style="display: {{ old('type', $lesson->type) === 'video' ? 'block' : 'none' }};">
                <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Video URL</label>
                <input type="url" id="video_url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                       placeholder="https://example.com/video.mp4">
            </div>

            <!-- Audio Upload Field -->
            <div id="audio-upload-field" class="mb-6" style="display: {{ old('type', $lesson->type) === 'audio' ? 'block' : 'none' }};">
                <label for="audio_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Audio File</label>
                <input type="file" id="audio_file" name="audio_file" accept="audio/*"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                @if($lesson->getFirstMediaUrl('audio'))
                    <div class="mt-2">
                        <audio controls class="w-full">
                            <source src="{{ $lesson->getFirstMediaUrl('audio') }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
            </div>

            <!-- PDF Upload Field -->
            <div id="pdf-upload-field" class="mb-6" style="display: {{ old('type', $lesson->type) === 'pdf' ? 'block' : 'none' }};">
                <label for="pdf_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PDF File</label>
                <input type="file" id="pdf_file" name="pdf_file" accept=".pdf"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                @if($lesson->getFirstMediaUrl('pdfs'))
                    <div class="mt-2">
                        <a href="{{ $lesson->getFirstMediaUrl('pdfs') }}" target="_blank" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            View Current PDF
                        </a>
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                <textarea id="content" name="content" rows="8"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('content', $lesson->content) }}</textarea>
            </div>

            <div class="mb-6">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lesson Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                @if($lesson->getFirstMediaUrl('thumbnails'))
                    <div class="mt-2">
                        <img src="{{ $lesson->getFirstMediaUrl('thumbnails') }}" alt="Current thumbnail" class="w-32 h-20 object-cover rounded">
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('teacher.courses.show', $lesson->course) }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Update Lesson
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    
    // Hide all type-specific fields
    document.getElementById('video-url-field').style.display = 'none';
    document.getElementById('audio-upload-field').style.display = 'none';
    document.getElementById('pdf-upload-field').style.display = 'none';
    
    // Show relevant field
    if (type === 'video') {
        document.getElementById('video-url-field').style.display = 'block';
    } else if (type === 'audio') {
        document.getElementById('audio-upload-field').style.display = 'block';
    } else if (type === 'pdf') {
        document.getElementById('pdf-upload-field').style.display = 'block';
    }
});
</script>
@endsection