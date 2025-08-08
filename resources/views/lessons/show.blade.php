@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Breadcrumb -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('courses.show', $course->slug) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                {{ $course->title }}
                            </a>
                        </li>
                        <li>
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </li>
                        <li class="text-gray-900 dark:text-white font-medium">{{ $lesson->title }}</li>
                    </ol>
                </nav>

                <!-- Lesson Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $lesson->title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                        <span class="capitalize">{{ $lesson->type }} Lesson</span>
                        @if($lesson->duration_minutes)
                            <span>{{ $lesson->duration_minutes }} minutes</span>
                        @endif
                        @if($progress && $progress->is_completed)
                            <span class="text-green-600 dark:text-green-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Completed
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Lesson Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                    <div id="lesson-warning" class="hidden text-red-600 text-sm font-medium mb-4"></div>
                    
                    <!-- Lesson Media Items -->
                    @if($lesson->lessonMedia->count() > 0)
                        <div class="space-y-6 mb-8">
                            @foreach($lesson->lessonMedia as $media)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                    @if($media->title || $media->description)
                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                            @if($media->title)
                                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $media->title }}</h4>
                                            @endif
                                            @if($media->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $media->description }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        @if($media->type === 'youtube')
                                            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                                <iframe src="{{ str_replace('watch?v=', 'embed/', $media->url) }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        @elseif($media->type === 'video')
                                            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                                <video class="w-full h-full" controls>
                                                    <source src="{{ $media->getMediaUrl() }}" type="video/mp4">
                                                </video>
                                            </div>
                                        @elseif($media->type === 'audio')
                                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-6">
                                                <audio class="w-full" controls>
                                                    <source src="{{ $media->getMediaUrl() }}" type="audio/mpeg">
                                                </audio>
                                            </div>
                                        @elseif($media->type === 'image')
                                            <div class="text-center">
                                                <img src="{{ $media->getMediaUrl() }}" alt="{{ $media->title }}" class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                                            </div>
                                        @elseif($media->type === 'pdf')
                                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                                <iframe src="{{ $media->getMediaUrl() }}#toolbar=1" width="100%" height="600px"></iframe>
                                            </div>
                                        @elseif($media->type === 'document')
                                            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 text-center">
                                                <svg class="w-16 h-16 mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">{{ $media->title ?: 'Document' }}</p>
                                                <a href="{{ $media->getMediaUrl() }}" download class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors inline-block">
                                                    Download
                                                </a>
                                            </div>
                                        @elseif($media->type === 'link')
                                            <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-lg p-6 text-center text-white">
                                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                <p class="text-lg font-medium mb-4">{{ $media->title ?: 'External Resource' }}</p>
                                                <a href="{{ $media->url }}" target="_blank" class="bg-white text-green-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors inline-block">
                                                    Open Link
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Text Content -->
                    @if($lesson->content)
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $lesson->content !!}
                        </div>
                    @endif
                </div>

                <!-- Quiz Section -->
                @if($lesson->quiz && $lesson->quiz->questions->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Lesson Quiz</h2>
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-blue-800 dark:text-blue-200 font-medium">{{ $lesson->quiz->questions->count() }} Questions</p>
                                    <p class="text-blue-600 dark:text-blue-400 text-sm">Passing score: {{ $lesson->quiz->passing_score }}%</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('quiz.show', $lesson->quiz) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-block">
                            Start Quiz
                        </a>
                    </div>
                @endif

                <!-- Discussions -->
                @auth
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Discussion</h2>
                        
                        <!-- Add Comment Form -->
                        <form id="discussion-form" class="mb-6" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="parent-id" name="parent_id" value="">
                            <div id="reply-info" class="hidden mb-2 p-2 bg-blue-50 dark:bg-blue-900 rounded text-sm">
                                <span>Replying to <strong id="reply-to-user"></strong></span>
                                <button type="button" id="cancel-reply" class="ml-2 text-red-600 hover:text-red-800">Cancel</button>
                            </div>
                            <textarea id="discussion-content" name="content" rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                      placeholder="Ask a question or share your thoughts about this lesson..."></textarea>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Attach Media</label>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <button type="button" id="upload-file-btn" class="flex items-center space-x-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <span>Upload</span>
                                    </button>
                                    <button type="button" id="record-audio-btn" class="flex items-center space-x-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                        <span>Record Audio</span>
                                    </button>
                                    <button type="button" id="record-video-btn" class="flex items-center space-x-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <span>Record Video</span>
                                    </button>
                                </div>
                                <input type="file" id="media-upload" name="media" accept="image/*,.pdf,.docx,.doc,audio/*,video/*" class="hidden">
                                <div id="media-recorder" class="hidden mt-3 p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-lg border">
                                    <video id="discussion-video-preview" class="w-full max-w-sm rounded-lg shadow-lg hidden" autoplay muted></video>
                                    <div id="discussion-audio-recorder" class="hidden text-center">
                                        <div class="bg-gradient-to-r from-red-500 to-pink-500 rounded-lg p-4 text-white mb-3">
                                            <div class="flex items-center justify-center space-x-3 mb-2">
                                                <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                                <span id="discussion-record-time" class="font-mono text-lg">00:00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <audio id="discussion-audio-preview" class="w-full mt-2 hidden" controls></audio>
                                    <div class="flex items-center justify-center space-x-3 mt-3">
                                        <button type="button" id="start-discussion-record" class="flex items-center space-x-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-full transition-all duration-200">
                                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                            <span>Start</span>
                                        </button>
                                        <button type="button" id="stop-discussion-record" class="hidden flex items-center space-x-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-full transition-all duration-200">
                                            <div class="w-2 h-2 bg-white"></div>
                                            <span>Stop</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="media-preview" class="mt-3 hidden">
                                    <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span id="media-name" class="text-sm font-medium text-gray-900"></span>
                                        </div>
                                        <button type="button" id="remove-media" class="text-red-600 hover:text-red-800 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Images, PDFs, Documents, Audio, Video - max 10MB</span>
                                </p>
                            </div>
                            <div class="flex justify-between items-center mt-3">
                                <div id="comment-count" class="text-sm text-gray-500 dark:text-gray-400"></div>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    Post Comment
                                </button>
                            </div>
                        </form>

                        <!-- Comments List -->
                        <div id="discussions-list">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <p>Loading discussions...</p>
                            </div>
                        </div>
                    </div>
                @endauth

                <!-- Navigation -->
                <div class="flex items-center justify-between">
                    @if($previousLesson)
                        <a href="{{ route('lessons.show', [$course->slug, $previousLesson->slug]) }}" 
                           class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Previous Lesson</span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('lessons.show', [$course->slug, $nextLesson->slug]) }}" 
                           class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <span>Next Lesson</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Course Progress</h3>
                    
                    @if($isEnrolled || $isTeacher)
                        @php
                            $enrollment = $isTeacher ? null : auth()->user()->enrollments()->where('course_id', $course->id)->first();
                        @endphp
                        <div class="mb-6">
                            @if($enrollment)
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Overall Progress</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->progress_percentage ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                </div>
                            @elseif($isTeacher)
                                <div class="text-center text-blue-600 dark:text-blue-400 text-sm font-medium">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Course Author
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Lesson List -->
                    <div class="space-y-2">
                        @foreach($course->lessons as $courseLesson)
                            <div class="flex items-center space-x-3 p-3 rounded-lg {{ $courseLesson->id === $lesson->id ? 'bg-blue-50 dark:bg-blue-900' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium
                                    @if(($isEnrolled || $isTeacher) && auth()->user()->lessonProgress()->where('lesson_id', $courseLesson->id)->where('is_completed', true)->exists())
                                        bg-green-500 text-white
                                    @elseif($courseLesson->id === $lesson->id)
                                        bg-blue-500 text-white
                                    @else
                                        bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400
                                    @endif
                                ">
                                    @if(($isEnrolled || $isTeacher) && auth()->user()->lessonProgress()->where('lesson_id', $courseLesson->id)->where('is_completed', true)->exists())
                                        ✓
                                    @else
                                        {{ $courseLesson->order }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($isEnrolled || $isTeacher || $courseLesson->is_free)
                                        <a href="{{ route('lessons.show', [$course->slug, $courseLesson->slug]) }}" 
                                           class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors block truncate">
                                            {{ $courseLesson->title }}
                                        </a>
                                    @else
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block truncate">
                                            {{ $courseLesson->title }}
                                        </span>
                                    @endif
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="capitalize">{{ $courseLesson->type }}</span>
                                        @if($courseLesson->duration_minutes)
                                            <span>{{ $courseLesson->duration_minutes }}m</span>
                                        @endif
                                        @if($courseLesson->is_free)
                                            <span class="text-green-600 dark:text-green-400">Free</span>
                                        @endif
                                    </div>
                                </div>
                                @if(!$isEnrolled && !$isTeacher && !$courseLesson->is_free)
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @auth
                    @if($lesson->quiz)
                        <a href="{{ route('quiz.show', $lesson->quiz) }}" 
                        class="w-full mt-6 bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-medium text-center block transition-colors">
                            Take Quiz
                        </a>
                    @endif
                    
                    @if($progress)
                        @if($progress->is_completed)
                            @if($progress->pending_approval)
                                <div class="w-full mt-3 bg-yellow-500 text-white px-4 py-3 rounded-lg font-medium text-center">
                                    ✓ Completed (Pending Teacher Approval)
                                </div>
                            @else
                                <div class="w-full mt-3 bg-green-500 text-white px-4 py-3 rounded-lg font-medium text-center">
                                    ✓ Completed
                                </div>
                            @endif
                        @else
                            @if(!$lesson->quiz)
                                <button onclick="markComplete()" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium text-center transition-colors">
                                    Mark as Complete
                                </button>
                            @else
                                <div class="w-full mt-3 bg-gray-400 text-white px-4 py-3 rounded-lg font-medium text-center text-sm">
                                    Complete quiz to unlock
                                </div>
                            @endif
                        @endif
                    @endif
                @endauth
                </div>
            </div>
        </div> 
    </div>
</div> 


@include('components.review-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if ($('#player').length) {
        const videoUrl = $('#player').data('plyr-embed-id');
        const lessonId = $('#player').data('lesson-id');

        // Use Plyr with encrypted or hidden video URL
        const player = new Plyr('#player', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            youtube: {
                noCookie: true,
                rel: 0,
                showinfo: 0,
                modestbranding: 1,
                controls: 1 // Ensure Plyr controls override YouTube's
            },
            source: {
                type: 'video',
                sources: [{
                    src: '/video-stream/' + lessonId, // Proxy endpoint to stream video
                    provider: 'html5'
                }]
            }
        });

        // Remove these event handlers
        /*
        let lastTrackedTime = 0;

        player.on('timeupdate', function(event) {
            const currentTime = Math.floor(player.currentTime);
            const duration = Math.floor(player.duration);
            const audioEnabled = !player.muted && player.volume > 0;

            if (currentTime - lastTrackedTime >= 5 && duration > 0) {
                $.post('/lessons/' + lessonId + '/video-progress', {
                    current_time: currentTime,a
                    duration: duration,
                    audio_enabled: audioEnabled,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }).done(function(response) {
                    $('#lesson-warning').removeClass('hidden').text(response.message);
                    if (response.video_completed) {
                        $('#lesson-warning').addClass('hidden');
                    }
                    if (response.trigger_review) {
                        setTimeout(() => {
                            showReviewModal(response.course_id);
                        }, 2000);
                    }
                }).fail(function(xhr) {
                    toastr.error('Error tracking video progress. Please try again.');
                });
                lastTrackedTime = currentTime;
            }
        });

        player.on('seeking', function(event) {
            if (player.currentTime > lastTrackedTime + 10) {
                player.currentTime = lastTrackedTime;
            }
        });
        */

        // Disable right-click to prevent context menu
        $('#player').on('contextmenu', function(e) {
            e.preventDefault();
        });
    }

    // Initialize audio/video players
    if ($('#lesson-audio').length) {
        const audioPlayer = new Plyr('#lesson-audio');
    }
    if ($('#lesson-video').length) {
        const videoPlayer = new Plyr('#lesson-video');
    }

    // Discussion media handling
    let discussionMediaRecorder;
    let discussionRecordedChunks = [];
    let discussionRecordingTimer;
    let discussionRecordingSeconds = 0;
    let discussionRecordingType = '';

    $('#upload-file-btn').on('click', function() {
        $('#media-upload').click();
    });

    $('#record-audio-btn').on('click', async function() {
        discussionRecordingType = 'audio';
        $('#media-recorder').removeClass('hidden');
        $('#discussion-video-preview').addClass('hidden');
        $('#discussion-audio-recorder').removeClass('hidden');
        await initDiscussionRecording(false);
    });

    $('#record-video-btn').on('click', async function() {
        discussionRecordingType = 'video';
        $('#media-recorder').removeClass('hidden');
        $('#discussion-video-preview').removeClass('hidden');
        $('#discussion-audio-recorder').addClass('hidden');
        await initDiscussionRecording(true);
    });

    $('#media-upload').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            $('#media-name').text(file.name);
            $('#media-preview').removeClass('hidden').hide().fadeIn(300);
            showDiscussionToast('File selected successfully!', 'success');
        }
    });

    $('#remove-media').on('click', function() {
        $('#media-upload').val('');
        $('#media-preview').addClass('hidden');
        $('#media-recorder').addClass('hidden');
        $('#discussion-audio-recorder').addClass('hidden');
        $('#discussion-video-preview').addClass('hidden');
    });

    async function initDiscussionRecording(includeVideo) {
        try {
            const constraints = includeVideo ? { video: true, audio: true } : { audio: true };
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            if (includeVideo) {
                $('#discussion-video-preview')[0].srcObject = stream;
            }
            
            $('#start-discussion-record').off('click').on('click', () => startDiscussionRecording(stream));
            $('#stop-discussion-record').off('click').on('click', () => stopDiscussionRecording());
        } catch (err) {
            alert('Media access denied or not available');
        }
    }

    function startDiscussionRecording(stream) {
        discussionRecordedChunks = [];
        discussionMediaRecorder = new MediaRecorder(stream);
        
        discussionMediaRecorder.ondataavailable = event => {
            if (event.data.size > 0) discussionRecordedChunks.push(event.data);
        };
        
        discussionMediaRecorder.onstop = () => {
            const mimeType = discussionRecordingType === 'video' ? 'video/webm' : 'audio/webm';
            const blob = new Blob(discussionRecordedChunks, { type: mimeType });
            const file = new File([blob], `recorded-${discussionRecordingType}.webm`, { type: blob.type });
            
            const dt = new DataTransfer();
            dt.items.add(file);
            $('#media-upload')[0].files = dt.files;
            
            $('#media-name').text(file.name);
            $('#media-preview').removeClass('hidden');
            
            if (discussionRecordingType === 'audio') {
                $('#discussion-audio-preview')[0].src = URL.createObjectURL(blob);
                $('#discussion-audio-preview').removeClass('hidden');
            }
            
            // Show success toast
            showDiscussionToast(`${discussionRecordingType.charAt(0).toUpperCase() + discussionRecordingType.slice(1)} recorded successfully!`, 'success');
        };
        
        discussionMediaRecorder.start();
        $('#start-discussion-record').addClass('hidden');
        $('#stop-discussion-record').removeClass('hidden');
        startDiscussionTimer();
    }

    function stopDiscussionRecording() {
        discussionMediaRecorder.stop();
        $('#start-discussion-record').removeClass('hidden');
        $('#stop-discussion-record').addClass('hidden');
        stopDiscussionTimer();
    }

    function startDiscussionTimer() {
        discussionRecordingSeconds = 0;
        discussionRecordingTimer = setInterval(() => {
            discussionRecordingSeconds++;
            const minutes = Math.floor(discussionRecordingSeconds / 60);
            const seconds = discussionRecordingSeconds % 60;
            $('#discussion-record-time').text(
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
            );
        }, 1000);
    }

    function stopDiscussionTimer() {
        clearInterval(discussionRecordingTimer);
    }

    function showDiscussionToast(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const toast = $(`<div class="fixed top-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        </div>`);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.css('transform', 'translateX(0)');
        }, 100);
        
        setTimeout(() => {
            toast.css('transform', 'translateX(100%)');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function renderMediaAttachment(mediaUrl, mediaType, size = 'sm') {
        const iconSize = size === 'xs' ? 'w-3 h-3' : 'w-4 h-4';
        const textSize = size === 'xs' ? 'text-xs' : 'text-sm';
        
        if (mediaType && (mediaType.includes('image') || ['jpg', 'jpeg', 'png', 'gif'].includes(mediaType))) {
            return `<div class="mb-2"><img src="${mediaUrl}" alt="Attachment" class="max-w-xs rounded border"></div>`;
        } else if (mediaType && (mediaType.includes('audio') || ['mp3', 'wav', 'ogg', 'webm'].includes(mediaType))) {
            return `<div class="mb-2"><audio controls class="w-full max-w-xs"><source src="${mediaUrl}"></audio></div>`;
        } else if (mediaType && (mediaType.includes('video') || ['mp4', 'webm', 'avi'].includes(mediaType))) {
            return `<div class="mb-2"><video controls class="w-full max-w-xs rounded"><source src="${mediaUrl}"></video></div>`;
        } else {
            return `<div class="mb-2"><a href="${mediaUrl}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 ${textSize}"><svg class="${iconSize} mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Download attachment</a></div>`;
        }
    }

    // Discussion form
    $('#discussion-form').on('submit', function(e) {
        e.preventDefault();
        
        const content = $('#discussion-content').val().trim();
        if (!content) return;

        const formData = new FormData();
        formData.append('content', content);
        const parentId = $('#parent-id').val();
        if (parentId && parentId !== '') {
            formData.append('parent_id', parentId);
        }
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        const mediaUpload = $('#media-upload')[0];
        if (mediaUpload && mediaUpload.files && mediaUpload.files[0]) {
            formData.append('media', mediaUpload.files[0]);
        }

        $.ajax({
            url: '{{ route("discussions.store", $lesson) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#discussion-content').val('');
                    $('#parent-id').val('');
                    $('#reply-info').addClass('hidden');
                    $('#media-upload').val('');
                    toastr.success(response.message);
                    loadDiscussions();
                    updateCommentCount();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.error) {
                    toastr.error(response.error);
                } else {
                    toastr.error('Error posting comment. Please try again.');
                }
            }
        });
    });

    // Load discussions
    function loadDiscussions() {
        $.get('{{ route("discussions.index", $lesson) }}', function(data) {
            if (data.discussions && data.discussions.length > 0) {
                let html = '';
                data.discussions.forEach(function(discussion) {
                    html += renderDiscussion(discussion);
                });
                $('#discussions-list').html(html);
            } else {
                $('#discussions-list').html('<div class="text-center py-8 text-gray-500 dark:text-gray-400"><p>No discussions yet. Be the first to ask a question!</p></div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading discussions:', error);
            $('#discussions-list').html('<div class="text-center py-8 text-red-500"><p>Error loading discussions. Please refresh the page.</p></div>');
        });
    }

    function renderDiscussion(discussion) {
        let repliesHtml = '';
        if (discussion.replies && discussion.replies.length > 0) {
            discussion.replies.forEach(function(reply) {
                const replyDate = new Date(reply.created_at);
                const timeAgo = getTimeAgo(replyDate);
                
                repliesHtml += `
                    <div class="ml-8 mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                ${reply.user.profile_picture_url ? 
                                    `<img src="${reply.user.profile_picture_url}" alt="${reply.user.name}" class="w-6 h-6 rounded-full object-cover">` :
                                    `<div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">${reply.user.name.charAt(0)}</span>
                                    </div>`
                                }
                                <span class="text-sm font-medium text-gray-900 dark:text-white">${reply.user.name}</span>
                                ${reply.user.is_course_author ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">Course Author</span>' : ''}
                                <span class="text-xs text-gray-500 dark:text-gray-400">${timeAgo}</span>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">${reply.content}</p>
                        ${reply.media_url ? `<div class="mb-2"><a href="${reply.media_url}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Download attachment</a></div>` : ''}
                        <button onclick="replyTo(${reply.id}, '${reply.user.name}')" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                            Reply
                        </button>
                    </div>
                `;
                
                // Render nested replies
                if (reply.replies && reply.replies.length > 0) {
                    reply.replies.forEach(function(subReply) {
                        const subReplyDate = new Date(subReply.created_at);
                        const subTimeAgo = getTimeAgo(subReplyDate);
                        
                        repliesHtml += `
                            <div class="ml-16 mt-3 p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        ${subReply.user.profile_picture_url ? 
                                            `<img src="${subReply.user.profile_picture_url}" alt="${subReply.user.name}" class="w-5 h-5 rounded-full object-cover">` :
                                            `<div class="w-5 h-5 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-medium">${subReply.user.name.charAt(0)}</span>
                                            </div>`
                                        }
                                        <span class="text-xs font-medium text-gray-900 dark:text-white">${subReply.user.name}</span>
                                        ${subReply.user.is_course_author ? '<span class="bg-blue-100 text-blue-800 px-1 py-0.5 rounded text-xs font-medium">Course Author</span>' : ''}
                                        <span class="text-xs text-gray-500 dark:text-gray-400">${subTimeAgo}</span>
                                    </div>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 text-xs mb-2">${subReply.content}</p>
                                ${subReply.media_url ? `<div class="mb-2"><a href="${subReply.media_url}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Download attachment</a></div>` : ''}
                                <button onclick="replyTo(${subReply.id}, '${subReply.user.name}')" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    Reply
                                </button>
                            </div>
                        `;
                    });
                }
            });
        }

        const discussionDate = new Date(discussion.created_at);
        const timeAgo = getTimeAgo(discussionDate);
        
        return `
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6 last:border-b-0 ${discussion.is_resolved ? 'opacity-75' : ''}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        ${discussion.user.profile_picture_url ? 
                            `<img src="${discussion.user.profile_picture_url}" alt="${discussion.user.name}" class="w-8 h-8 rounded-full object-cover">` :
                            `<div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">${discussion.user.name.charAt(0)}</span>
                            </div>`
                        }
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-gray-900 dark:text-white">${discussion.user.name}</span>
                            ${discussion.user.is_course_author ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">Course Author</span>' : ''}
                            <span class="text-sm text-gray-500 dark:text-gray-400">${timeAgo}</span>
                        </div>
                    </div>
                    ${discussion.is_resolved ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Resolved</span>' : ''}
                </div>
                <p class="text-gray-700 dark:text-gray-300 mb-3">${discussion.content}</p>
                ${discussion.media_url ? renderMediaAttachment(discussion.media_url, discussion.media_type) : ''}
                <div class="flex items-center space-x-4">
                    <button onclick="replyTo(${discussion.id}, '${discussion.user.name}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Reply
                    </button>
                    @if(auth()->id() === $lesson->course->teacher_id)
                        <button onclick="toggleResolve(${discussion.id})" class="${discussion.is_resolved ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'} text-sm font-medium">
                            ${discussion.is_resolved ? 'Reopen' : 'Mark Resolved'}
                        </button>
                    @endif
                </div>
                ${repliesHtml}
            </div>
        `;
    }
    
    function getTimeAgo(date) {
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric',
            year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
        });
    }

    window.replyTo = function(discussionId, userName) {
        $('#parent-id').val(discussionId);
        $('#reply-to-user').text(userName);
        $('#reply-info').removeClass('hidden');
        $('#discussion-content').focus();
    };

    $('#cancel-reply').on('click', function() {
        $('#parent-id').val('');
        $('#reply-info').addClass('hidden');
    });

    window.moderateComment = function(discussionId) {
        if (confirm('Are you sure you want to hide this comment?')) {
            $.post('/discussions/' + discussionId + '/moderate', {
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    loadDiscussions();
                }
            });
        }
    };
    
    window.toggleResolve = function(discussionId) {
        $.post('/discussions/' + discussionId + '/resolve', {
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
                loadDiscussions();
            }
        }).fail(function(xhr) {
            const response = xhr.responseJSON;
            toastr.error(response?.error || 'Error updating discussion status');
        });
    };

    $('.modal-star').on('click', function() {
        const rating = $(this).data('rating');
        $('#modal-rating-value').val(rating);
        
        $('.modal-star').each(function(index) {
            if (index < rating) {
                $(this).removeClass('text-gray-300').addClass('text-yellow-400');
            } else {
                $(this).removeClass('text-yellow-400').addClass('text-gray-300');
            }
        });
    });

    $('#modal-rating-form').on('submit', function(e) {
        e.preventDefault();
        
        const rating = $('#modal-rating-value').val();
        const comment = $('#modal-review-comment').val();
        
        if (rating == 0) {
            toastr.error('Please select a rating');
            return;
        }

        $.ajax({
            url: '/courses/{{ $course->id }}/review',
            method: 'POST',
            data: {
                rating: rating,
                comment: comment,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#review-modal').addClass('hidden');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.error || 'Error submitting review');
            }
        });
    });

    $('#skip-review').on('click', function() {
        $('#review-modal').addClass('hidden');
    });

    function updateCommentCount() {
        $.get('{{ route("discussions.count", $lesson) }}', function(data) {
            $('#comment-count').text(`${data.count}/5 comments used`);
        });
    }

    @auth
        loadDiscussions();
        updateCommentCount();
    @endauth

    // Global function to show review modal
    window.showReviewModal = function(courseId) {
        $('#review-modal').removeClass('hidden');
    };

    // Mark lesson as complete
    window.markComplete = function() {
        $.post('{{ route("lessons.complete", $lesson) }}', {
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            if (response.success) {
                toastr.success('Lesson marked as complete!');
                
                if (response.trigger_review && response.course_id) {
                    setTimeout(() => {
                        showReviewModal(response.course_id);
                    }, 1500);
                } else {
                    location.reload();
                }
            }
        }).fail(function() {
            toastr.error('Error marking lesson as complete');
        });
    };
});
</script>
@endpush