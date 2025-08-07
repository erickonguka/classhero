<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'answers',
        'score',
        'total_points',
        'passed',
        'started_at',
        'completed_at',
        'time_taken',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_points' => 'integer',
        'passed' => 'boolean',
        'time_taken' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}