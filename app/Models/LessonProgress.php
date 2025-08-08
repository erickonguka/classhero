<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_completed',
        'time_spent',
        'started_at',
        'completed_at',
        'video_watched_seconds',
        'video_completed',
        'quiz_passed',
        'comment_posted',
        'pending_approval', // New field
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'time_spent' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'video_completed' => 'boolean',
        'quiz_passed' => 'boolean',
        'comment_posted' => 'boolean',
        'pending_approval' => 'boolean', // New cast
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}