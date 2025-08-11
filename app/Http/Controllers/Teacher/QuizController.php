<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::whereHas('lesson.course', function($q) {
            $q->where('teacher_id', Auth::id());
        })->with(['lesson.course', 'questions']);

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->course_id) {
            $query->whereHas('lesson', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $quizzes = $query->paginate(15);
        $courses = Auth::user()->courses;

        return view('teacher.quizzes.index', compact('quizzes', 'courses'));
    }

    public function show(Quiz $quiz)
    {
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $quiz->load(['questions', 'attempts.user']);
        return view('teacher.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $quiz->load('questions');
        return view('teacher.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1',
            'time_limit' => 'nullable|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,fill_blank',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answers' => 'required|array',
            'questions.*.points' => 'required|integer|min:1|max:10',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'passing_score' => $request->passing_score,
            'max_attempts' => $request->max_attempts,
            'time_limit' => $request->time_limit,
        ]);

        // Delete existing questions and recreate
        $quiz->questions()->delete();

        foreach ($request->questions as $index => $questionData) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'options' => $questionData['options'] ?? null,
                'correct_answers' => $questionData['correct_answers'],
                'explanation' => $questionData['explanation'] ?? null,
                'points' => $questionData['points'],
                'order' => $index + 1,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Quiz updated successfully!',
                'redirect_url' => route('teacher.quizzes.show', $quiz)
            ]);
        }
        
        return redirect()->route('teacher.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $quiz->delete();
        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'Quiz deleted successfully!');
    }
}