<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions' => function($query) {
            $query->orderBy('order');
        }, 'lesson.course']);

        // Check if user can access this quiz
        $isEnrolled = Auth::user()->enrollments()
            ->where('course_id', $quiz->lesson->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'You need to be enrolled in this course to take the quiz.');
        }

        // Check attempts
        $attempts = $quiz->userAttempts(Auth::id())->get();
        $canTakeQuiz = $attempts->count() < $quiz->max_attempts;

        if (!$canTakeQuiz) {
            return redirect()->back()->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        return view('quiz.show', compact('quiz', 'attempts', 'canTakeQuiz'));
    }

    public function start(Quiz $quiz)
    {
        $attempts = $quiz->userAttempts(Auth::id())->get();
        
        if ($attempts->count() >= $quiz->max_attempts) {
            return response()->json(['error' => 'Maximum attempts reached'], 400);
        }

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'answers' => [],
            'started_at' => now(),
        ]);

        return response()->json(['attempt_id' => $attempt->id]);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'No active attempt found'], 400);
        }

        $answers = $request->input('answers', []);
        $score = 0;
        $totalPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;
            
            if ($userAnswer && $this->isCorrectAnswer($question, $userAnswer)) {
                $score += $question->points;
            }
        }

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100) : 0;
        $passed = $percentage >= $quiz->passing_score;

        $attempt->update([
            'answers' => $answers,
            'score' => $percentage,
            'total_points' => $totalPoints,
            'passed' => $passed,
            'completed_at' => now(),
            'time_taken' => now()->diffInSeconds($attempt->started_at),
        ]);

        // Update LessonProgress
        $progress = LessonProgress::firstOrCreate([
            'user_id' => Auth::id(),
            'lesson_id' => $quiz->lesson_id,
        ]);

        if ($passed) {
            $progress->update(['quiz_passed' => true]);
        }

        // Check lesson completion (only quiz and comment required)
        $lesson = $quiz->lesson;
        $completed = true;

        // Remove video completion check
        // if ($lesson->require_video_completion && !$progress->video_completed) {
        //     $completed = false;
        // }

        if ($lesson->require_quiz_pass && !$progress->quiz_passed) {
            $completed = false;
        }

        if ($lesson->require_comment && !$progress->comment_posted) {
            $completed = false;
        }

        if ($completed && !$progress->is_completed) {
            $progress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Update enrollment progress
            $enrollment = $lesson->course->enrollments()->where('user_id', Auth::id())->first();
            if ($enrollment) {
                $totalLessons = $lesson->course->lessons()->count();
                $completedLessons = $lesson->course->lessons()
                    ->whereHas('progress', fn($query) => $query->where('user_id', Auth::id())->where('is_completed', true))
                    ->count();
                $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                $enrollment->update([
                    'lessons_completed' => $completedLessons,
                    'total_lessons' => $totalLessons,
                    'progress_percentage' => $progressPercentage,
                    'completed_at' => $progressPercentage === 100 ? now() : null,
                ]);
            }
        }

        // Award points if passed
        if ($passed) {
            Auth::user()->increment('points', 20);
        }

        return response()->json([
            'score' => $percentage,
            'passed' => $passed,
            'passing_score' => $quiz->passing_score,
            'redirect' => route('lessons.show', [$lesson->course->slug, $lesson->slug]),
        ]);
    }

    private function isCorrectAnswer($question, $userAnswer)
    {
        switch ($question->type) {
            case 'multiple_choice':
                return in_array($userAnswer, $question->correct_answers);
            case 'true_false':
                return $userAnswer === $question->correct_answers[0];
            case 'fill_blank':
                return in_array(strtolower(trim($userAnswer)), array_map('strtolower', $question->correct_answers));
            default:
                return false;
        }
    }
}