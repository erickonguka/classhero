@extends('layouts.app')

@section('title', 'Create Quiz')

@section('content')
<div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create Quiz</h1>
            <p class="text-gray-600 dark:text-gray-400">Add a quiz for {{ $lesson->title }}</p>
        </div>

        <form action="{{ route('teacher.lessons.quiz.store', $lesson) }}" method="POST" id="quiz-form" data-ajax="true">
            @csrf

            <!-- Quiz Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Quiz Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quiz Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $lesson->title . ' Quiz') }}" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="passing_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passing Score (%)</label>
                        <input type="number" id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" min="1" max="100" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Attempts</label>
                        <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 3) }}" min="1" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label for="time_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Limit (minutes)</label>
                        <input type="number" id="time_limit" name="time_limit" value="{{ old('time_limit', 10) }}" min="1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave empty for no time limit</p>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Questions</h2>
                    <button type="button" id="add-question" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Add Question
                    </button>
                </div>

                <div id="questions-container">
                    <!-- Questions will be added here -->
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between">
                <a href="{{ route('teacher.lessons.show', $lesson) }}" class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium">
                    Cancel
                </a>
                <x-spinning-button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    Create Quiz
                </x-spinning-button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}"></script>
<script>
let questionIndex = 0;

$(document).ready(function() {
    // Add first question by default
    addNewQuestion();

    // Handle question type changes
    $(document).on('change', '.question-type', function() {
        toggleQuestionOptions($(this));
    });
    
    // Form submission with validation
    $('#quiz-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate multiple choice questions have at least one correct answer
        let isValid = true;
        $('.question-item').each(function() {
            const questionType = $(this).find('.question-type').val();
            if (questionType === 'multiple_choice') {
                const checkedAnswers = $(this).find('input[type="checkbox"]:checked').length;
                if (checkedAnswers === 0) {
                    isValid = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Each multiple choice question must have at least one correct answer marked.',
                        confirmButtonColor: '#3b82f6'
                    });
                    return false;
                }
            }
        });
        
        if (isValid) {
            // Submit via AJAX
            const formData = new FormData(this);
            const submitButton = $(this).find('button[type="submit"]');
            
            submitButton.prop('disabled', true).find('.spinner').removeClass('hidden');
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        if (response.redirect_url) {
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1500);
                        }
                    }
                },
                error: function(xhr) {
                    let message = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message);
                },
                complete: function() {
                    submitButton.prop('disabled', false).find('.spinner').addClass('hidden');
                }
            });
        }
    });
    
    // Add new question
    $('#add-question').on('click', function() {
        addNewQuestion();
    });
    
    // Remove question
    $(document).on('click', '.remove-question', function() {
        $(this).closest('.question-item').remove();
        updateQuestionNumbers();
    });
    
    // Add option for multiple choice
    $(document).on('click', '.add-option', function() {
        const questionIndex = $(this).data('question');
        const optionsList = $(this).siblings('.options-list');
        const optionCount = optionsList.children().length;
        
        optionsList.append(`
            <div class="flex items-center space-x-2 mb-2">
                <input type="text" name="questions[${questionIndex}][options][]" 
                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                       placeholder="Option ${optionCount + 1}" required>
                <label class="flex items-center">
                    <input type="checkbox" name="questions[${questionIndex}][correct_answers][]" value="${optionCount}" 
                           class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                    <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">Correct</span>
                </label>
                <button type="button" class="remove-option text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `);
    });
    
    // Remove option
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.flex').remove();
    });
});

function toggleQuestionOptions(selectElement) {
    const questionItem = selectElement.closest('.question-item');
    const type = selectElement.val();
    const optionsContainer = questionItem.find('.options-container');
    
    if (type === 'multiple_choice') {
        const questionIndex = selectElement.attr('name').match(/\[(\d+)\]/)[1];
        optionsContainer.html(`
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</label>
            <div class="options-list">
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="questions[${questionIndex}][options][]" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                           placeholder="Option 1" required>
                    <label class="flex items-center">
                        <input type="checkbox" name="questions[${questionIndex}][correct_answers][]" value="0" 
                               class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                        <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">Correct</span>
                    </label>
                    <button type="button" class="remove-option text-red-600 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="questions[${questionIndex}][options][]" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                           placeholder="Option 2" required>
                    <label class="flex items-center">
                        <input type="checkbox" name="questions[${questionIndex}][correct_answers][]" value="1" 
                               class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                        <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">Correct</span>
                    </label>
                    <button type="button" class="remove-option text-red-600 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="button" class="add-option text-blue-600 hover:text-blue-700 text-sm font-medium" data-question="${questionIndex}">
                + Add Option
            </button>
        `);
    } else if (type === 'true_false') {
        const questionIndex = selectElement.attr('name').match(/\[(\d+)\]/)[1];
        optionsContainer.html(`
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer</label>
            <select name="questions[${questionIndex}][correct_answers][]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                <option value="true">True</option>
                <option value="false">False</option>
            </select>
        `);
    } else if (type === 'fill_blank') {
        const questionIndex = selectElement.attr('name').match(/\[(\d+)\]/)[1];
        optionsContainer.html(`
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer</label>
            <input type="text" name="questions[${questionIndex}][correct_answers][]" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                   placeholder="Enter the correct answer" required>
        `);
    }
}

function addNewQuestion() {
    const container = $('#questions-container');
    const questionHtml = `
        <div class="question-item border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question ${questionIndex + 1}</h3>
                <button type="button" class="remove-question text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question</label>
                    <textarea name="questions[${questionIndex}][question]" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Enter your question"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                        <select name="questions[${questionIndex}][type]" class="question-type w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="fill_blank">Fill in the Blank</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                        <input type="number" name="questions[${questionIndex}][points]" value="10" min="1" max="10" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="options-container"></div>
            </div>
        </div>
    `;
    
    container.append(questionHtml);
    
    // Initialize the new question's options
    const newQuestion = container.find('.question-item').last();
    const selectElement = newQuestion.find('.question-type');
    toggleQuestionOptions(selectElement);
    
    questionIndex++;
    updateQuestionNumbers();
}

function updateQuestionNumbers() {
    $('.question-item').each(function(index) {
        $(this).find('h3').text('Question ' + (index + 1));
    });
}
</script>
@endpush