<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now      = now();
        $monthAgo = $now->copy()->subDays(30);
        $prevMonth = $now->copy()->subDays(60);

        // Students
        $studentsTotal     = User::where('role', User::ROLE_STUDENT)->count();
        $studentsRecent    = User::where('role', User::ROLE_STUDENT)->where('created_at', '>=', $monthAgo)->count();
        $studentsPrev      = User::where('role', User::ROLE_STUDENT)->whereBetween('created_at', [$prevMonth, $monthAgo])->count();
        $studentsDelta     = $this->deltaPercent($studentsRecent, $studentsPrev);

        // Teachers
        $teachersApproved  = User::where('role', User::ROLE_TEACHER)->where('is_approved', true)->count();
        $teachersPending   = User::pendingTeachers()->count();

        // Courses
        $coursesPublished  = Course::where('is_published', true)->count();
        $coursesDraft      = Course::where('is_published', false)->count();

        // Attempts (30d)
        $attempts30        = QuizAttempt::where('submitted_at', '>=', $monthAgo)->count();
        $attemptsPrev30    = QuizAttempt::whereBetween('submitted_at', [$prevMonth, $monthAgo])->count();
        $attemptsDelta     = $this->deltaPercent($attempts30, $attemptsPrev30);

        return [
            Stat::make('Students', $studentsTotal)
                ->description($studentsDelta . ' this month')
                ->descriptionIcon($studentsRecent >= $studentsPrev ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->dailyCounts(User::class, ['role' => User::ROLE_STUDENT]))
                ->color($studentsRecent >= $studentsPrev ? 'success' : 'warning'),

            Stat::make('Teachers', $teachersApproved)
                ->description($teachersPending > 0 ? "{$teachersPending} pending approval" : 'All caught up')
                ->descriptionIcon($teachersPending > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->chart($this->dailyCounts(User::class, ['role' => User::ROLE_TEACHER, 'is_approved' => true]))
                ->color($teachersPending > 0 ? 'warning' : 'success'),

            Stat::make('Courses', $coursesPublished)
                ->description($coursesDraft > 0 ? "{$coursesDraft} in draft" : 'No drafts')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->chart($this->dailyCounts(Course::class, ['is_published' => true]))
                ->color('primary'),

            Stat::make('Attempts · 30d', $attempts30)
                ->description($attemptsDelta . ' vs prev 30d')
                ->descriptionIcon($attempts30 >= $attemptsPrev30 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->dailyCounts(QuizAttempt::class, [], 'submitted_at'))
                ->color($attempts30 >= $attemptsPrev30 ? 'success' : 'danger'),
        ];
    }

    private function deltaPercent(int $current, int $previous): string
    {
        if ($previous === 0) {
            return $current === 0 ? '0%' : '+'.$current;
        }
        $pct = (($current - $previous) / $previous) * 100;
        $sign = $pct >= 0 ? '+' : '';
        return $sign . number_format($pct, 0) . '%';
    }

    private function dailyCounts(string $model, array $where = [], string $column = 'created_at'): array
    {
        $start = now()->subDays(13)->startOfDay();
        $rows  = $model::query()
            ->where(function ($q) use ($where) {
                foreach ($where as $k => $v) {
                    $q->where($k, $v);
                }
            })
            ->where($column, '>=', $start)
            ->selectRaw("DATE($column) as d, COUNT(*) as c")
            ->groupBy('d')
            ->pluck('c', 'd');

        $out = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $out[] = (int) ($rows[$day] ?? 0);
        }
        return $out;
    }
}
