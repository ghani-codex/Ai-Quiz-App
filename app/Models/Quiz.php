<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    protected $fillable = [
        'lecture_id',
        'title',
        'difficulty',
        'question_count',
        'time_limit_minutes',
        'is_published',
        'ai_generation_status',
        'ai_provider_used',
        'ai_tokens_used',
        'ai_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published'    => 'boolean',
            'ai_generated_at' => 'datetime',
        ];
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeReady($query)
    {
        return $query->where('ai_generation_status', 'ready');
    }
}
