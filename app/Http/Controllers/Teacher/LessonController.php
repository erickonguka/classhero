<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Quiz;
use App\Models\QuizQuestion;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        $lessons = $course->lessons()->orderBy('order')->paginate(10);
        return view('teacher.lessons.index', compact('course', 'lessons'));
    }

    public function create(Course $course)
    {
        return view('teacher.lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'content' => 'required|string',
            'is_free' => 'boolean',
            'require_video_completion' => 'boolean',
            'require_quiz_pass' => 'boolean',
            'require_comment' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048', // Max 2MB
            'media_files.*' => 'nullable|file|max:102400', // Max 100MB per file
            'video_url' => 'nullable|url',
            'external_url' => 'nullable|url',
            'media_data' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Generate slug from title
        $data['slug'] = Str::slug($data['title']);

        // Determine lesson type based on media
        $mediaData = !empty($data['media_data']) ? json_decode($data['media_data'], true) : [];
        $lessonType = $this->determineLessonType($mediaData, $request->file('media_files'), $data['video_url'], $data['external_url']);

        // Create the lesson
        $lesson = new Lesson();
        $lesson->course_id = $course->id;
        $lesson->title = $data['title'];
        $lesson->slug = $data['slug'];
        $lesson->description = $data['description'];
        $lesson->duration_minutes = $data['duration_minutes'];
        $lesson->content = $data['content'];
        $lesson->type = $lessonType;
        $lesson->is_free = $data['is_free'] ?? false;
        $lesson->require_video_completion = $data['require_video_completion'] ?? false;
        $lesson->require_quiz_pass = $data['require_quiz_pass'] ?? false;
        $lesson->require_comment = $data['require_comment'] ?? false;
        $lesson->order = $course->lessons()->count() + 1;

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $lesson->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $lesson->save();

        // Handle media uploads
        $this->storeMedia($lesson, $request->file('media_files'), $mediaData, $data['video_url'], $data['external_url']);

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully!',
            'redirect' => route('teacher.courses.show', $course)
        ]);
    }

    public function show(Course $course, Lesson $lesson)
    {
        return view('teacher.lessons.show', compact('course', 'lesson'));
    }

    public function edit(Course $course, Lesson $lesson)
    {
        $existingMedia = $lesson->lessonMedia->map(function ($media) {
            return [
                'id'          => $media->id,
                'type'        => $media->type,
                'url'         => $media->url ?? ($media->file_path ? asset('storage/' . $media->file_path) : null),
                'title'       => $media->title,
                'description' => $media->description,
                'order'       => $media->order,
            ];
        })->toArray();

        return view('teacher.lessons.edit', compact('course', 'lesson', 'existingMedia'));
    }


    public function update(Request $request, Lesson $lesson)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'content' => 'required|string',
            'is_free' => 'boolean',
            'require_video_completion' => 'boolean',
            'require_quiz_pass' => 'boolean',
            'require_comment' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048', // Max 2MB
            'media_files.*' => 'nullable|file|max:102400', // Max 100MB per file
            'video_url' => 'nullable|url',
            'external_url' => 'nullable|url',
            'media_data' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Update slug if title changed
        $data['slug'] = Str::slug($data['title']);

        // Determine lesson type based on media
        $mediaData = !empty($data['media_data']) ? json_decode($data['media_data'], true) : [];
        $lessonType = $this->determineLessonType($mediaData, $request->file('media_files'), $data['video_url'], $data['external_url']);

        // Update the lesson
        $lesson->title = $data['title'];
        $lesson->slug = $data['slug'];
        $lesson->description = $data['description'];
        $lesson->duration_minutes = $data['duration_minutes'];
        $lesson->content = $data['content'];
        $lesson->type = $lessonType;
        $lesson->is_free = $data['is_free'] ?? false;
        $lesson->require_video_completion = $data['require_video_completion'] ?? false;
        $lesson->require_quiz_pass = $data['require_quiz_pass'] ?? false;
        $lesson->require_comment = $data['require_comment'] ?? false;

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            if ($lesson->thumbnail) {
                Storage::disk('public')->delete($lesson->thumbnail);
            }
            $lesson->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $lesson->save();

        // Handle media uploads
        $this->storeMedia($lesson, $request->file('media_files'), $mediaData, $data['video_url'], $data['external_url']);

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully!',
            'redirect' => route('teacher.courses.show', $lesson->course)
        ]);
    }

    public function destroy(Lesson $lesson)
    {
        // Delete associated media files
        if ($lesson->thumbnail) {
            Storage::disk('public')->delete($lesson->thumbnail);
        }

        foreach ($lesson->lessonMedia as $media) {
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }
            $media->delete();
        }

        // Delete the lesson
        $lesson->delete();

        // Reorder remaining lessons
        $lesson->course->lessons()->orderBy('order')->get()->each(function ($remainingLesson, $index) {
            $remainingLesson->order = $index + 1;
            $remainingLesson->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully!',
            'redirect' => route('teacher.courses.show', $lesson->course)
        ]);
    }

    protected function determineLessonType(array $mediaData, $mediaFiles, $videoUrl, $externalUrl)
    {
        $mediaTypes = [];

        // Collect media types from media_data
        foreach ($mediaData as $media) {
            if (!empty($media['type'])) {
                $mediaTypes[] = $media['type'];
            }
        }

        // Collect media types from uploaded files
        if ($mediaFiles) {
            foreach ($mediaFiles as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (in_array($extension, ['mp4', 'webm', 'avi', 'mov', 'mkv'])) {
                    $mediaTypes[] = 'video';
                } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'aac', 'm4a'])) {
                    $mediaTypes[] = 'audio';
                } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $mediaTypes[] = 'image';
                } elseif ($extension === 'pdf') {
                    $mediaTypes[] = 'pdf';
                } elseif (in_array($extension, ['docx', 'doc'])) {
                    $mediaTypes[] = 'document';
                }
            }
        }

        // Check for YouTube or external URLs
        if ($videoUrl && (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be'))) {
            $mediaTypes[] = 'youtube';
        }
        if ($externalUrl && str_starts_with($externalUrl, 'http')) {
            $mediaTypes[] = 'link';
        }

        // Determine lesson type based on priority
        if (in_array('video', $mediaTypes) || in_array('youtube', $mediaTypes)) {
            return 'video';
        } elseif (in_array('audio', $mediaTypes)) {
            return 'audio';
        } elseif (in_array('pdf', $mediaTypes)) {
            return 'pdf';
        } elseif (in_array('document', $mediaTypes)) {
            return 'document';
        } elseif (in_array('link', $mediaTypes)) {
            return 'link';
        }

        return 'text'; // Default if no media is provided
    }

    protected function storeMedia(Lesson $lesson, $mediaFiles, array $mediaData, $videoUrl, $externalUrl)
    {
        // Delete existing media if updating
        if ($lesson->lessonMedia()->exists()) {
            foreach ($lesson->lessonMedia as $media) {
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->delete();
            }
        }

        // Store new media
        foreach ($mediaData as $index => $media) {
            $lessonMedia = new LessonMedia();
            $lessonMedia->lesson_id = $lesson->id;
            $lessonMedia->type = $media['type'];
            $lessonMedia->title = $media['title'] ?? '';
            $lessonMedia->description = $media['description'] ?? '';
            $lessonMedia->url = $media['url'] ?? null;
            $lessonMedia->order = $media['order'] ?? $index;

            if ($media['type'] === 'youtube' && $videoUrl) {
                $lessonMedia->url = $videoUrl;
            } elseif ($media['type'] === 'link' && $externalUrl) {
                $lessonMedia->url = $externalUrl;
            }

            $lessonMedia->save();
        }

        // Store uploaded files
        if ($mediaFiles) {
            foreach ($mediaFiles as $index => $file) {
                $path = $file->store('lesson_media', 'public');
                $extension = strtolower($file->getClientOriginalExtension());
                $type = $this->getMediaTypeFromExtension($extension);

                $lessonMedia = new LessonMedia();
                $lessonMedia->lesson_id = $lesson->id;
                $lessonMedia->type = $type;
                $lessonMedia->title = $mediaData[$index]['title'] ?? $file->getClientOriginalName();
                $lessonMedia->description = $mediaData[$index]['description'] ?? '';
                $lessonMedia->file_path = $path;
                $lessonMedia->order = $mediaData[$index]['order'] ?? $index;
                $lessonMedia->save();
            }
        }
    }

    protected function getMediaTypeFromExtension($extension)
    {
        $videoExts = ['mp4', 'webm', 'avi', 'mov', 'mkv'];
        $audioExts = ['mp3', 'wav', 'ogg', 'aac', 'm4a'];
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $docExts = ['docx', 'doc'];

        if (in_array($extension, $videoExts)) return 'video';
        if (in_array($extension, $audioExts)) return 'audio';
        if (in_array($extension, $imageExts)) return 'image';
        if ($extension === 'pdf') return 'pdf';
        if (in_array($extension, $docExts)) return 'document';
        return 'file';
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
            'questions.*.points' => 'required|integer|min:1|max:10',
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Quiz created successfully!',
                'redirect_url' => route('teacher.lessons.show', $lesson)
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