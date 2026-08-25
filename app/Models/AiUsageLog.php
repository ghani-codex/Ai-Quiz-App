<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'model',
        'input_tokens',
        'output_tokens',
        'status',
        'user_id',
        'related_quiz_id',
        'error_message',
        'latency_ms',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedQuiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'related_quiz_id');
    }
}
