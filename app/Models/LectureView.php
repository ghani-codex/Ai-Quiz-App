<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureView extends Model
{
    protected $fillable = [
        'user_id',
        'lecture_id',
        'first_viewed_at',
        'last_viewed_at',
        'view_count',
        'downloaded_at',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'first_viewed_at' => 'datetime',
            'last_viewed_at'  => 'datetime',
            'downloaded_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    /**
     * Record a view for a (user, lecture) pair. First call creates the row,
     * subsequent calls bump view_count and last_viewed_at.
     */
    public static function record(int $userId, int $lectureId): self
    {
        $view = static::firstOrNew([
            'user_id'    => $userId,
            'lecture_id' => $lectureId,
        ]);

        if ($view->exists) {
            $view->view_count++;
        } else {
            $view->first_viewed_at = now();
            $view->view_count = 1;
        }
        $view->last_viewed_at = now();
        $view->save();

        return $view;
    }

    /**
     * Mark a download for (user, lecture). Auto-records a view if not yet seen.
     */
    public static function recordDownload(int $userId, int $lectureId): self
    {
        $view = static::record($userId, $lectureId);
        $view->downloaded_at = now();
        $view->download_count++;
        $view->save();

        return $view;
    }
}
