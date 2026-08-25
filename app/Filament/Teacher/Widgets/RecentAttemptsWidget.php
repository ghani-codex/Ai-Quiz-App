<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\QuizAttempt;
use Filament\Widgets\Widget;

class RecentAttemptsWidget extends Widget
{
    protected static string $view = 'filament.teacher.widgets.recent-attempts';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAttempts(): array
    {
        return QuizAttempt::query()
            ->whereHas('quiz.lecture.course', fn ($q) => $q->where('teacher_id', auth()->id()))
            ->whereNotNull('submitted_at')
            ->with(['user:id,name', 'quiz:id,title,lecture_id', 'quiz.lecture:id,title,course_id', 'quiz.lecture.course:id,title'])
            ->latest('submitted_at')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'id'           => $a->id,
                'score'        => (float) ($a->score ?? 0),
                'submitted_at' => $a->submitted_at,
                'student'      => $a->user?->name ?? 'Unknown',
                'quiz'         => $a->quiz?->title ?? '—',
                'course'       => $a->quiz?->lecture?->course?->title ?? '—',
                'time_taken'   => (int) ($a->time_taken_seconds ?? 0),
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function getStats(array $attempts): array
    {
        $count = count($attempts);
        if ($count === 0) {
            return [
                'count'      => 0,
                'avg_score'  => 0,
                'pass_rate'  => 0,
                'top_score'  => 0,
            ];
        }

        $scores  = collect($attempts)->pluck('score');
        $passed  = $scores->filter(fn ($s) => $s >= 50)->count();

        return [
            'count'      => $count,
            'avg_score'  => round($scores->avg(), 1),
            'pass_rate'  => (int) round(($passed / $count) * 100),
            'top_score'  => round($scores->max(), 1),
        ];
    }
}
