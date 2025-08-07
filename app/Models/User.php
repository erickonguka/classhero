<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'points',
        'preferred_categories',
        'country_code',
        'phone',
        'currency',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
            'preferred_categories' => 'array',
        ];
    }

    // Relationships
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }
    
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isLearner()
    {
        return $this->role === 'learner';
    }

    public function getProfilePictureUrl()
    {
        $url = $this->getFirstMediaUrl('profile_pictures');
        return $url ? asset($url) : null;
    }

    public function getCurrencySymbol()
    {
        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'CAD' => 'C$',
            'AUD' => 'A$', 'CNY' => '¥', 'INR' => '₹', 'BRL' => 'R$', 'ZAR' => 'R',
            'NGN' => '₦', 'KES' => 'KSh', 'EGP' => 'E£', 'SAR' => 'SR', 'AED' => 'د.إ',
            'SGD' => 'S$', 'MYR' => 'RM', 'THB' => '฿', 'PHP' => '₱', 'IDR' => 'Rp',
            'KRW' => '₩', 'TWD' => 'NT$', 'HKD' => 'HK$', 'PKR' => 'Rs'
        ];
        return $symbols[$this->currency] ?? '$';
    }

    public function getCountryName()
    {
        $countries = [
            'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada',
            'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France', 'IN' => 'India',
            'NG' => 'Nigeria', 'ZA' => 'South Africa', 'BR' => 'Brazil',
            'JP' => 'Japan', 'CN' => 'China'
        ];
        return $countries[$this->country_code] ?? $this->country_code;
    }
}