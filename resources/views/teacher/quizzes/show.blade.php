@extends('layouts.teacher')

@section('title', $quiz->title)
@section('page-title', $quiz->title)

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <p class="text-gray-600 dark:text-gray-400">{{ $quiz->lesson->course->title }} • {{ $quiz->lesson->title }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                Edit Quiz
            </a>
            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Delete this quiz?')" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $quiz->questions->count() }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Questions</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $quiz->passing_score }}%</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Passing Score</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $quiz->max_attempts }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Max Attempts</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $quiz->attempts->count() }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Attempts</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Questions</h2>
        <div class="space-y-4">
            @foreach($quiz->questions as $question)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $question->question }}</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        Type: {{ ucfirst(str_replace('_', ' ', $question->type)) }} • Points: {{ $question->points }}
                    </div>
                    @if($question->options)
                        <div class="ml-4">
                            @foreach($question->options as $index => $option)
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm {{ in_array($index, $question->correct_answers) ? 'text-green-600 font-medium' : 'text-gray-600' }}">
                                        {{ chr(65 + $index) }}. {{ $option }}
                                        @if(in_array($index, $question->correct_answers))
                                            ✓
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection