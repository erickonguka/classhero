<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LessonMedia extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'lesson_id',
        'type',
        'title',
        'description',
        'url',
        'file_path',
        'order'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getMediaUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        
        $media = $this->getFirstMedia('files');
        return $media ? $media->getUrl() : null;
    }
}