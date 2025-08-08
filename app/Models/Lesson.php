<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'course_id',
        'description',
        'content',
        'type',
        'video_url',
        'external_url',
        'duration_minutes',
        'order',
        'is_free',
        'is_published',
        'completion_requirements',
        'require_video_completion',
        'require_quiz_pass',
        'require_comment',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
        'completion_requirements' => 'array',
        'require_video_completion' => 'boolean',
        'require_quiz_pass' => 'boolean',
        'require_comment' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function userProgress($userId)
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getMediaUrl($collection = 'default')
    {
        $media = $this->getFirstMedia($collection);
        return $media ? $media->getUrl() : null;
    }

    public function lessonMedia()
    {
        return $this->hasMany(LessonMedia::class)->orderBy('order');
    }
}