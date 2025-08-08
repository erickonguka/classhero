@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="bg-gradient-to-br from-indigo-50 to-purple-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('courses.show', $quiz->lesson->course->slug) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ $quiz->lesson->course->title }}
                    </a>
                </li>
                <li>
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </li>
                <li>
                    <a href="{{ route('lessons.show', [$quiz->lesson->course->slug, $quiz->lesson->slug]) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ $quiz->lesson->title }}
                    </a>
                </li>
                <li>
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </li>
                <li class="text-gray-900 dark:text-white font-medium">{{ $quiz->title }}</li>
            </ol>
        </nav>

        <!-- Quiz Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $quiz->title }}</h1>
                @if($quiz->description)
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $quiz->description }}</p>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $quiz->questions->count() }}</div>
                        <div class="text-sm text-blue-800 dark:text-blue-200">Questions</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $quiz->passing_score }}%</div>
                        <div class="text-sm text-green-800 dark:text-green-200">Passing Score</div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            @if($quiz->time_limit)
                                {{ $quiz->time_limit }} min
                            @else
                                No Limit
                            @endif
                        </div>
                        <div class="text-sm text-orange-800 dark:text-orange-200">Time Limit</div>
                    </div>
                </div>

                @if($attempts->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Previous Attempts</h3>
                        <div class="space-y-2">
                            @foreach($attempts as $attempt)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        Attempt {{ $loop->iteration }} - {{ $attempt->completed_at->format('M j, Y g:i A') }}
                                    </span>
                                    <span class="font-medium {{ $attempt->passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $attempt->score }}% {{ $attempt->passed ? '(Passed)' : '(Failed)' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $quiz->max_attempts - $attempts->count() }} attempts remaining
                        </p>
                    </div>
                @endif

                @if($canTakeQuiz)
                    <button id="start-quiz" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                        {{ $attempts->count() > 0 ? 'Retake Quiz' : 'Start Quiz' }}
                    </button>
                    <a href="{{ route('lessons.show', [$quiz->lesson->course->slug, $quiz->lesson->slug]) }}" 
                    class="inline-block ml-4 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold text-lg transition-colors">
                        Back to Lesson
                    </a>
                @else
                    <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                        <p class="text-red-800 dark:text-red-200">You have reached the maximum number of attempts for this quiz.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quiz Interface (Hidden initially) -->
        <div id="quiz-interface" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <!-- Quiz Timer -->
                @if($quiz->time_limit)
                    <div class="flex items-center justify-center mb-6">
                        <div class="bg-red-100 dark:bg-red-900 rounded-lg px-4 py-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="timer" class="font-mono text-lg font-bold text-red-600 dark:text-red-400">{{ $quiz->time_limit }}:00</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                        <span id="progress-text" class="text-sm text-gray-600 dark:text-gray-400">0 / {{ $quiz->questions->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Questions -->
                <form id="quiz-form">
                    @foreach($quiz->questions as $index => $question)
                        <div class="question-slide {{ $index === 0 ? 'block' : 'hidden' }}" data-question="{{ $index + 1 }}">
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                    Question {{ $index + 1 }} of {{ $quiz->questions->count() }}
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-6">{{ $question->question }}</p>

                                @if($question->type === 'multiple_choice')
                                    <div class="space-y-3">
                                        @foreach($question->options as $optionIndex => $option)
                                            <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionIndex }}" 
                                                       class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                                <span class="ml-3 text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($question->type === 'true_false')
                                    <div class="space-y-3">
                                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="true" 
                                                   class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                            <span class="ml-3 text-gray-700 dark:text-gray-300">True</span>
                                        </label>
                                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="false" 
                                                   class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                            <span class="ml-3 text-gray-700 dark:text-gray-300">False</span>
                                        </label>
                                    </div>
                                @elseif($question->type === 'fill_blank')
                                    <input type="text" name="answers[{{ $question->id }}]" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="Enter your answer">
                                @endif
                            </div>

                            <div class="flex items-center justify-between">
                                @if($index > 0)
                                    <button type="button" class="prev-question bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                        Previous
                                    </button>
                                @else
                                    <div></div>
                                @endif

                                @if($index < $quiz->questions->count() - 1)
                                    <button type="button" class="next-question bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                        Next
                                    </button>
                                @else
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                        Submit Quiz
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>

        <!-- Results Modal -->
        <div id="results-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 max-w-md w-full mx-4">
                <div class="text-center">
                    <div id="result-icon" class="w-16 h-16 mx-auto mb-4"></div>
                    <h3 id="result-title" class="text-2xl font-bold mb-2"></h3>
                    <p id="result-score" class="text-4xl font-bold mb-4"></p>
                    <p id="result-message" class="text-gray-600 dark:text-gray-400 mb-6"></p>
                    <button id="close-results" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentQuestion = 0;
    let totalQuestions = {{ $quiz->questions->count() }};
    let timeLimit = {{ $quiz->time_limit ?? 0 }};
    let timeRemaining = timeLimit * 60;
    let timerInterval;
    let attemptId = null;

    // Start quiz
    $('#start-quiz').on('click', function() {
        $.ajax({
            url: '{{ route("quiz.start", $quiz) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                attemptId = response.attempt_id;
                $('.bg-white.dark\\:bg-gray-800.rounded-xl.shadow-lg.p-8.mb-8').hide();
                $('#quiz-interface').removeClass('hidden');
                
                if (timeLimit > 0) {
                    startTimer();
                }
                updateProgress();
            },
            error: function() {
                alert('Error starting quiz. Please try again.');
            }
        });
    });

    // Timer
    function startTimer() {
        timerInterval = setInterval(function() {
            timeRemaining--;
            let minutes = Math.floor(timeRemaining / 60);
            let seconds = timeRemaining % 60;
            $('#timer').text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                submitQuiz();
            }
        }, 1000);
    }

    // Navigation
    $('.next-question').on('click', function() {
        if (currentQuestion < totalQuestions - 1) {
            $('.question-slide').eq(currentQuestion).addClass('hidden');
            currentQuestion++;
            $('.question-slide').eq(currentQuestion).removeClass('hidden');
            updateProgress();
        }
    });

    $('.prev-question').on('click', function() {
        if (currentQuestion > 0) {
            $('.question-slide').eq(currentQuestion).addClass('hidden');
            currentQuestion--;
            $('.question-slide').eq(currentQuestion).removeClass('hidden');
            updateProgress();
        }
    });

    function updateProgress() {
        let progress = ((currentQuestion + 1) / totalQuestions) * 100;
        $('#progress-bar').css('width', progress + '%');
        $('#progress-text').text((currentQuestion + 1) + ' / ' + totalQuestions);
    }

    // Submit quiz
    $('#quiz-form').on('submit', function(e) {
        e.preventDefault();
        submitQuiz();
    });

    function submitQuiz() {
        if (timerInterval) {
            clearInterval(timerInterval);
        }

        let formData = $('#quiz-form').serialize();
        
        $.ajax({
            url: '{{ route("quiz.submit", $quiz) }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showResults(response);
            },
            error: function() {
                alert('Error submitting quiz. Please try again.');
            }
        });
    }

    function showResults(results) {
        let passed = results.passed;
        let score = results.score;
        
        if (passed) {
            $('#result-icon').html(`
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            `);
            $('#result-title').text('Congratulations!').addClass('text-green-600 dark:text-green-400');
            $('#result-message').text('You passed the quiz! +20 points earned.');
        } else {
            $('#result-icon').html(`
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            `);
            $('#result-title').text('Try Again').addClass('text-red-600 dark:text-red-400');
            $('#result-message').text('You need ' + results.passing_score + '% to pass. Keep studying!');
        }
        
        $('#result-score').text(score + '%');
        $('#results-modal').removeClass('hidden');
    }

    $('#close-results').on('click', function() {
        location.reload();
    });
});
</script>
@endpush