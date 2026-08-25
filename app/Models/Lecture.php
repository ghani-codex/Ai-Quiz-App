<?php

namespace App\Models;

use App\Services\AI\LectureTextExtractor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'content_type',
        'file_path',
        'raw_text',
        'order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(LectureView::class);
    }

    /**
     * On save: if a PDF/DOCX file was just attached and the teacher didn't
     * paste raw text, auto-extract text from the file so the AI quiz
     * generator has something to read.
     */
    protected static function booted(): void
    {
        static::saving(function (self $lecture) {
            $needsExtraction = in_array($lecture->content_type, ['pdf', 'docx'], true)
                && $lecture->isDirty('file_path')
                && ! empty($lecture->file_path)
                && empty(trim((string) $lecture->raw_text));

            if (! $needsExtraction) {
                return;
            }

            $extracted = app(LectureTextExtractor::class)->extract('local', $lecture->file_path);

            if ($extracted !== '') {
                $lecture->raw_text = $extracted;
            }
        });
    }
}
