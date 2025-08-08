<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'lessons_completed',
        'total_lessons',
        'rating',
        'review',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'progress_percentage' => 'integer',
        'lessons_completed' => 'integer',
        'total_lessons' => 'integer',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class, 'user_id', 'user_id');
    }

    public function certificate()
    {
        return Certificate::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->first();
    }
    
    public function certificateRelation()
    {
        return $this->hasOne(Certificate::class, 'user_id', 'user_id')
            ->where('certificates.course_id', $this->course_id);
    }

    public function recalculateProgress()
    {
        $totalLessons = $this->course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $this->user_id)
            ->whereHas('lesson', function($query) {
                $query->where('course_id', $this->course_id);
            })
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Reset completion status if progress is no longer 100% due to new lessons
        $updateData = [
            'total_lessons' => $totalLessons,
            'lessons_completed' => $completedLessons,
            'progress_percentage' => $progressPercentage,
        ];

        // If progress dropped below 100%, reset completion status
        if ($progressPercentage < 100 && $this->completed_at) {
            $updateData['completed_at'] = null;
        }

        $this->update($updateData);

        return $this;
    }

    public function isFullyCompleted()
    {
        return $this->progress_percentage == 100 && $this->course->lessons()->count() > 0;
    }
}