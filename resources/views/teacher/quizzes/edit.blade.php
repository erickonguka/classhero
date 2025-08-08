@extends('layouts.teacher')

@section('title', 'Edit Quiz')
@section('page-title', 'Edit Quiz')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('teacher.quizzes.update', $quiz) }}">
            @csrf
            @method('PUT')
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Quiz Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description', $quiz->description) }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passing Score (%)</label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="1" max="100" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Attempts</label>
                        <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Limit (minutes)</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Questions</h2>
                <div id="questions-container">
                    @foreach($quiz->questions as $index => $question)
                        <div class="question-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Question {{ $index + 1 }}</h3>
                                <button type="button" class="remove-question text-red-600 hover:text-red-700">Remove</button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                                    <textarea name="questions[{{ $index }}][question]" rows="2" required
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ $question->question }}</textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                                    <select name="questions[{{ $index }}][type]" class="question-type w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                                        <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                        <option value="true_false" {{ $question->type === 'true_false' ? 'selected' : '' }}>True/False</option>
                                        <option value="fill_blank" {{ $question->type === 'fill_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                                    <input type="number" name="questions[{{ $index }}][points]" value="{{ $question->points }}" min="1" required
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                            
                            <div class="options-container">
                                @if($question->type === 'multiple_choice' && $question->options)
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</label>
                                    <div class="options-list">
                                        @foreach($question->options as $optIndex => $option)
                                            <div class="flex items-center space-x-2 mb-2">
                                                <input type="text" name="questions[{{ $index }}][options][]" value="{{ $option }}" placeholder="Option {{ $optIndex + 1 }}"
                                                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="questions[{{ $index }}][correct_answers][]" value="{{ $optIndex }}" 
                                                           {{ in_array($optIndex, $question->correct_answers ?? []) ? 'checked' : '' }}
                                                           class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                                    <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">Correct</span>
                                                </label>
                                                <button type="button" class="remove-option text-red-600 hover:text-red-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-option text-blue-600 hover:text-blue-700 text-sm font-medium" data-question="{{ $index }}">
                                        + Add Option
                                    </button>
                                @elseif($question->type === 'true_false')
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer</label>
                                    <select name="questions[{{ $index }}][correct_answers][]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                                        <option value="true" {{ (isset($question->correct_answers[0]) && $question->correct_answers[0] === 'true') ? 'selected' : '' }}>True</option>
                                        <option value="false" {{ (isset($question->correct_answers[0]) && $question->correct_answers[0] === 'false') ? 'selected' : '' }}>False</option>
                                    </select>
                                @elseif($question->type === 'fill_blank')
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer</label>
                                    <input type="text" name="questions[{{ $index }}][correct_answers][]" value="{{ $question->correct_answers[0] ?? '' }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="Enter the correct answer" required>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <button type="button" id="add-question" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Add Question
                </button>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    Update Quiz
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let questionIndex = {{ $quiz->questions->count() }};

$(document).ready(function() {
    // Initialize existing questions
    $('.question-type').each(function() {
        toggleQuestionOptions($(this));
    });
    
    // Handle question type changes
    $(document).on('change', '.question-type', function() {
        toggleQuestionOptions($(this));
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
        <div class="question-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Question ${questionIndex + 1}</h3>
                <button type="button" class="remove-question text-red-600 hover:text-red-700">Remove</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                    <textarea name="questions[${questionIndex}][question]" rows="2" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <select name="questions[${questionIndex}][type]" class="question-type w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="fill_blank">Fill in the Blank</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                    <input type="number" name="questions[${questionIndex}][points]" value="10" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="options-container"></div>
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
@endsection