<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'progress_pct',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at'  => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Live progress %: viewed lectures / total lectures in the course.
     * Pulls from `lecture_views` so it stays accurate even when lectures
     * are added after enrolment.
     */
    public function liveProgress(): int
    {
        $lectureIds = $this->course?->lectures()->pluck('id') ?? collect();
        $total = $lectureIds->count();

        if ($total === 0) {
            return 0;
        }

        $viewed = LectureView::where('user_id', $this->user_id)
            ->whereIn('lecture_id', $lectureIds)
            ->count();

        return (int) round(($viewed / $total) * 100);
    }

    /**
     * Sync the stored progress_pct + completed_at columns from live view data.
     */
    public function syncProgress(): void
    {
        $pct = $this->liveProgress();

        $this->update([
            'progress_pct' => $pct,
            'completed_at' => $pct >= 100 ? ($this->completed_at ?? now()) : null,
        ]);
    }
}
