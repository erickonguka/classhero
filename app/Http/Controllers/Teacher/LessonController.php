<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $lessons = $course->lessons()->orderBy('order')->get();
        return view('teacher.lessons.index', compact('course', 'lessons'));
    }

    public function create(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('teacher.lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:video,audio,pdf,text',
            'video_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_free' => 'boolean',
        ]);

        $lesson = Lesson::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'course_id' => $course->id,
            'content' => $request->content,
            'type' => $request->type,
            'video_url' => $request->video_url,
            'duration_minutes' => $request->duration_minutes,
            'order' => $course->lessons()->count() + 1,
            'is_free' => $request->boolean('is_free'),
            'is_published' => true,
        ]);

        return redirect()->route('teacher.courses.lessons.index', $course)
            ->with('success', 'Lesson created successfully!');
    }

    public function show(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('teacher.lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('teacher.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'type' => 'required|in:video,audio,pdf,text',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'video_url' => 'nullable|url',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:10240',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $lesson->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'content' => $request->content,
            'type' => $request->type,
            'order' => $request->order,
            'duration_minutes' => $request->duration_minutes,
            'video_url' => $request->video_url,
        ]);

        if ($request->hasFile('thumbnail')) {
            $lesson->clearMediaCollection('thumbnails');
            $lesson->addMediaFromRequest('thumbnail')
                   ->toMediaCollection('thumbnails');
        }

        if ($request->hasFile('audio_file')) {
            $lesson->clearMediaCollection('audio');
            $lesson->addMediaFromRequest('audio_file')
                   ->toMediaCollection('audio');
        }

        if ($request->hasFile('pdf_file')) {
            $lesson->clearMediaCollection('pdfs');
            $lesson->addMediaFromRequest('pdf_file')
                   ->toMediaCollection('pdfs');
        }

        return redirect()->route('teacher.courses.show', $lesson->course)
            ->with('success', 'Lesson updated successfully!');
    }

    public function destroy(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $lesson->delete();
        return redirect()->route('teacher.courses.lessons.index', $lesson->course)
            ->with('success', 'Lesson deleted successfully!');
    }

    public function createQuiz(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('teacher.quizzes.create', compact('lesson'));
    }

    public function storeQuiz(Request $request, Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
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
            'questions.*.points' => 'required|integer|min:1',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'lesson_id' => $lesson->id,
            'passing_score' => $request->passing_score,
            'max_attempts' => $request->max_attempts,
            'time_limit' => $request->time_limit,
            'is_required' => true,
            'show_results' => true,
        ]);

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

        return redirect()->route('teacher.lessons.show', $lesson)
            ->with('success', 'Quiz created successfully!');
    }

    public function comments(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }
        
        $discussions = $lesson->discussions()
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10);
            
        return view('teacher.lessons.comments', compact('lesson', 'discussions'));
    }
}