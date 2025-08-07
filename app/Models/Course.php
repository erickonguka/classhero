<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'overview',
        'curriculum',
        'instructor_info',
        'teacher_id',
        'category_id',
        'difficulty',
        'status',
        'is_free',
        'price',
        'duration_hours',
        'total_lessons',
        'enrolled_count',
        'rating',
        'rating_count',
        'requirements',
        'what_you_learn',
        'has_certificate',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'requirements' => 'array',
        'what_you_learn' => 'array',
        'curriculum' => 'array',
        'total_lessons' => 'integer',
        'enrolled_count' => 'integer',
        'rating_count' => 'integer',
        'duration_hours' => 'integer',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating') ?? 0;
        $this->update(['rating' => round($avgRating, 1)]);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function canBePublished()
    {
        return $this->lessons()->count() >= 1;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}