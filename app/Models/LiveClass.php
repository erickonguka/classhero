<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_password',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}