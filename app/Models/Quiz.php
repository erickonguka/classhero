<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'lesson_id',
        'passing_score',
        'max_attempts',
        'time_limit',
        'is_required',
        'show_results',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'time_limit' => 'integer',
        'is_required' => 'boolean',
        'show_results' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function userAttempts($userId)
    {
        return $this->attempts()->where('user_id', $userId);
    }
}