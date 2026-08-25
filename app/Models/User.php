<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'avatar_path',
        'bio',
        'country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Public URL for the user's avatar — used by Filament's header dropdown
     * and any view via $user->getFilamentAvatarUrl().
     * Falls back to ui-avatars.com when no upload exists so the header never
     * shows a broken image.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return Storage::disk('public')->url($this->avatar_path);
        }

        return 'https://ui-avatars.com/api/?background=4154f1&color=fff&size=128&name=' . urlencode($this->name ?: 'User');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin'   => $this->role === self::ROLE_ADMIN,
            'teacher' => $this->role === self::ROLE_TEACHER && $this->is_approved,
            'student' => $this->role === self::ROLE_STUDENT,
            default   => false,
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function lectureViews(): HasMany
    {
        return $this->hasMany(LectureView::class);
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', self::ROLE_TEACHER);
    }

    public function scopeStudents($query)
    {
        return $query->where('role', self::ROLE_STUDENT);
    }

    public function scopePendingTeachers($query)
    {
        return $query->where('role', self::ROLE_TEACHER)->where('is_approved', false);
    }
}
