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

        <form action="{{ route('teacher.lessons.quiz.store', $lesson) }}" method="POST" id="quiz-form">
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
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    Create Quiz
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let questionCount = 0;

$(document).ready(function() {
    // Add first question by default
    addQuestion();

    $('#add-question').on('click', function() {
        addQuestion();
    });

    $(document).on('click', '.remove-question', function() {
        $(this).closest('.question-item').remove();
        updateQuestionNumbers();
    });

    $(document).on('change', '.question-type', function() {
        const questionItem = $(this).closest('.question-item');
        const type = $(this).val();
        const optionsContainer = questionItem.find('.options-container');
        
        if (type === 'multiple_choice') {
            optionsContainer.show();
            optionsContainer.find('input').attr('required', true);
        } else {
            optionsContainer.hide();
            optionsContainer.find('input').attr('required', false);
        }
    });

    $(document).on('click', '.add-option', function() {
        const optionsList = $(this).siblings('.options-list');
        const optionCount = optionsList.children().length;
        
        optionsList.append(`
            <div class="flex items-center space-x-2 mb-2">
                <input type="text" name="questions[${$(this).data('question')}][options][]" 
                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                       placeholder="Option ${optionCount + 1}" required>
                <button type="button" class="remove-option text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `);
    });

    $(document).on('click', '.remove-option', function() {
        $(this).closest('.flex').remove();
    });
});

function addQuestion() {
    questionCount++;
    
    const questionHtml = `
        <div class="question-item border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question ${questionCount}</h3>
                <button type="button" class="remove-question text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question</label>
                    <textarea name="questions[${questionCount}][question]" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Enter your question"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                        <select name="questions[${questionCount}][type]" class="question-type w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="fill_blank">Fill in the Blank</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                        <input type="number" name="questions[${questionCount}][points]" value="10" min="1" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="options-container">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</label>
                    <div class="options-list">
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="text" name="questions[${questionCount}][options][]" 
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="Option 1" required>
                        </div>
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="text" name="questions[${questionCount}][options][]" 
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="Option 2" required>
                        </div>
                    </div>
                    <button type="button" class="add-option text-blue-600 hover:text-blue-700 text-sm font-medium" data-question="${questionCount}">
                        + Add Option
                    </button>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer(s)</label>
                    <input type="text" name="questions[${questionCount}][correct_answers][]" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                           placeholder="For multiple choice: 0,1 (option indices). For true/false: true or false. For fill blank: correct answer">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">For multiple choice, use option indices (0, 1, 2, etc.)</p>
                </div>
            </div>
        </div>
    `;
    
    $('#questions-container').append(questionHtml);
}

function updateQuestionNumbers() {
    $('.question-item').each(function(index) {
        $(this).find('h3').text('Question ' + (index + 1));
    });
}
</script>
@endpush