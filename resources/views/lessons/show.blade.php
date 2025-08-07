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
                    @if($lesson->type === 'video')
                        <div class="mb-6">
                            @if($lesson->video_url)
                                <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                    @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                        <iframe src="{{ $lesson->video_url }}" 
                                                class="w-full h-full" 
                                                frameborder="0" 
                                                allowfullscreen></iframe>
                                    @else
                                        <video id="lesson-video" class="w-full h-full" controls data-lesson-id="{{ $lesson->id }}">
                                            <source src="{{ $lesson->video_url }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </div>
                            @else
                                <div class="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4h10a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">Video Content</p>
                                        <p class="text-blue-200">Video will be available here</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($lesson->type === 'audio')
                        <div class="mb-6">
                            @if($lesson->audio_url)
                                <audio id="lesson-audio" class="w-full" controls>
                                    <source src="{{ $lesson->audio_url }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            @else
                                <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-lg p-8 text-center text-white">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M5 7h4l1 1h4a2 2 0 012 2v4a2 2 0 01-2 2H9l-1 1H5a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Audio Content</p>
                                    <p class="text-green-200">Audio will be available here</p>
                                </div>
                            @endif
                        </div>
                    @elseif($lesson->type === 'pdf')
                        <div class="mb-6">
                            <div id="pdf-viewer" class="border border-gray-300 dark:border-gray-600 rounded-lg" style="height: 600px;">
                                <div class="flex items-center justify-center h-full bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">PDF Content</p>
                                        <p>PDF viewer will be available here</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Text Content -->
                    @if($lesson->content)
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($lesson->content)) !!}
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

                <!-- Course Rating -->
                @auth
                    @if($isEnrolled)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Rate This Course</h2>
                            <form id="rating-form" class="mb-6">
                                @csrf
                                <div class="flex items-center space-x-2 mb-4">
                                    <span class="text-gray-700 dark:text-gray-300">Your Rating:</span>
                                    <div class="flex space-x-1" id="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="star text-2xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">
                                                ★
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" id="rating-value" name="rating" value="0">
                                </div>
                                <textarea id="review-comment" name="comment" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                          placeholder="Share your thoughts about this course (optional)..."></textarea>
                                <div class="flex justify-end mt-3">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                        Submit Review
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endauth

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
                            @if(auth()->user()->isTeacher() && auth()->id() == $course->teacher_id)
                                <div class="mt-3">
                                    <label for="media-upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attach Media (Teachers only)</label>
                                    <input type="file" id="media-upload" name="media" accept="image/*,video/*,audio/*,.pdf" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                </div>
                            @endif
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
                    
                    @if($isEnrolled)
                        @php
                            $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();
                        @endphp
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Overall Progress</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->progress_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Lesson List -->
                    <div class="space-y-2">
                        @foreach($course->lessons as $courseLesson)
                            <div class="flex items-center space-x-3 p-3 rounded-lg {{ $courseLesson->id === $lesson->id ? 'bg-blue-50 dark:bg-blue-900' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium
                                    @if($isEnrolled && auth()->user()->lessonProgress()->where('lesson_id', $courseLesson->id)->where('is_completed', true)->exists())
                                        bg-green-500 text-white
                                    @elseif($courseLesson->id === $lesson->id)
                                        bg-blue-500 text-white
                                    @else
                                        bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400
                                    @endif
                                ">
                                    @if($isEnrolled && auth()->user()->lessonProgress()->where('lesson_id', $courseLesson->id)->where('is_completed', true)->exists())
                                        ✓
                                    @else
                                        {{ $courseLesson->order }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($isEnrolled || $courseLesson->is_free)
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
                                @if(!$isEnrolled && !$courseLesson->is_free)
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
                        
                        @if($progress && $progress->is_completed)
                            <div class="w-full mt-3 bg-green-500 text-white px-4 py-3 rounded-lg font-medium text-center">
                                ✓ Completed
                            </div>
                        @else
                            <div class="w-full mt-3 bg-gray-400 text-white px-4 py-3 rounded-lg font-medium text-center text-sm">
                                Complete requirements to unlock
                            </div>
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
    // Initialize video player with tracking
    if ($('#lesson-video').length) {
        const video = document.getElementById('lesson-video');
        const lessonId = video.dataset.lessonId;
        let lastTrackedTime = 0;
        
        video.addEventListener('timeupdate', function() {
            const currentTime = Math.floor(video.currentTime);
            const duration = Math.floor(video.duration);
            const audioEnabled = !video.muted && video.volume > 0;
            
            // Track every 5 seconds
            if (currentTime - lastTrackedTime >= 5) {
                $.post('/lessons/' + lessonId + '/video-progress', {
                    current_time: currentTime,
                    duration: duration,
                    audio_enabled: audioEnabled,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }).done(function(response) {
                    if (response.trigger_review) {
                        setTimeout(() => {
                            showReviewModal(response.course_id);
                        }, 2000);
                    }
                });
                lastTrackedTime = currentTime;
            }
        });
        
        // Prevent seeking ahead without watching
        video.addEventListener('seeking', function() {
            if (video.currentTime > lastTrackedTime + 10) {
                video.currentTime = lastTrackedTime;
            }
        });
        
        const player = new Plyr('#lesson-video');
    }

    // Initialize audio player
    if ($('#lesson-audio').length) {
        const player = new Plyr('#lesson-audio');
    }

    // Remove manual complete button - completion is automatic based on requirements

    // Discussion form
    $('#discussion-form').on('submit', function(e) {
        e.preventDefault();
        
        const content = $('#discussion-content').val().trim();
        if (!content) return;

        const formData = new FormData();
        formData.append('content', content);
        formData.append('parent_id', $('#parent-id').val() || null);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        const mediaFile = $('#media-upload')[0].files[0];
        if (mediaFile) {
            formData.append('media', mediaFile);
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
        });
    }

    function renderDiscussion(discussion) {
        let repliesHtml = '';
        if (discussion.replies && discussion.replies.length > 0) {
            discussion.replies.forEach(function(reply) {
                repliesHtml += `
                    <div class="ml-8 mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-medium">${reply.user.name.charAt(0)}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${reply.user.name}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">${new Date(reply.created_at).toLocaleDateString()}</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">${reply.content}</p>
                    </div>
                `;
            });
        }

        return `
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6 last:border-b-0">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">${discussion.user.name.charAt(0)}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white">${discussion.user.name}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">${new Date(discussion.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 mb-3">${discussion.content}</p>
                <div class="flex items-center space-x-4">
                    <button onclick="replyTo(${discussion.id}, '${discussion.user.name}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Reply
                    </button>
                    @if(auth()->user()->isTeacher() && auth()->id() == $course->teacher_id)
                        <button onclick="moderateComment(${discussion.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Hide
                        </button>
                    @endif
                </div>
                ${repliesHtml}
            </div>
        `;
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

    // Star rating functionality
    $('.star').on('click', function() {
        const rating = $(this).data('rating');
        $('#rating-value').val(rating);
        
        $('.star').each(function(index) {
            if (index < rating) {
                $(this).removeClass('text-gray-300').addClass('text-yellow-400');
            } else {
                $(this).removeClass('text-yellow-400').addClass('text-gray-300');
            }
        });
    });

    // Rating form submission
    $('#rating-form').on('submit', function(e) {
        e.preventDefault();
        
        const rating = $('#rating-value').val();
        const comment = $('#review-comment').val();
        
        if (rating == 0) {
            toastr.error('Please select a rating');
            return;
        }

        $.ajax({
            url: '{{ route("courses.review", $course) }}',
            method: 'POST',
            data: {
                rating: rating,
                comment: comment,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#rating-form')[0].reset();
                    $('#rating-value').val(0);
                    $('.star').removeClass('text-yellow-400').addClass('text-gray-300');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.error) {
                    toastr.error(response.error);
                } else {
                    toastr.error('Error submitting review. Please try again.');
                }
            }
        });
    });

    // Comment moderation
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

    // Review modal functionality
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
        const userComments = {{ auth()->user()->discussions()->where('lesson_id', $lesson->id)->whereNull('parent_id')->count() }};
        $('#comment-count').text(`${userComments}/5 comments used`);
    }

    // Load discussions on page load
    @auth
        loadDiscussions();
        updateCommentCount();
    @endauth
});

// Global function to show review modal
window.showReviewModal = function(courseId) {
    $('#review-modal').removeClass('hidden');
};
</script>
@endpush