<?php

namespace App\Filament\Student\Widgets;

use App\Models\Enrollment;
use App\Models\LectureView;
use App\Models\QuizAttempt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $enrolledCount = Enrollment::where('user_id', $userId)->count();
        $completedCount = Enrollment::where('user_id', $userId)->whereNotNull('completed_at')->count();

        $lecturesRead = LectureView::where('user_id', $userId)->count();
        $downloads    = LectureView::where('user_id', $userId)->whereNotNull('downloaded_at')->count();

        $attempts = QuizAttempt::where('user_id', $userId);
        $attemptsTaken = (clone $attempts)->whereNotNull('submitted_at')->count();
        $avgScore      = (clone $attempts)->whereNotNull('score')->avg('score');
        $bestScore     = (clone $attempts)->whereNotNull('score')->max('score');

        return [
            Stat::make('Courses enrolled', $enrolledCount)
                ->description($completedCount . ' completed')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Lectures read', $lecturesRead)
                ->description($downloads > 0 ? "{$downloads} downloaded" : 'Open one from Course Player')
                ->descriptionIcon('heroicon-m-book-open')
                ->color($lecturesRead > 0 ? 'info' : 'gray'),

            Stat::make('Quizzes taken', $attemptsTaken)
                ->description($attemptsTaken > 0 ? 'Keep it up!' : 'Take your first quiz')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('Average score', $avgScore ? number_format($avgScore, 1) . '/100' : '—')
                ->description($avgScore ? ($avgScore >= 70 ? 'Excellent' : ($avgScore >= 50 ? 'Solid — keep practising' : 'Review the basics')) : 'No attempts yet')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgScore && $avgScore >= 70 ? 'success' : ($avgScore && $avgScore >= 50 ? 'warning' : 'gray')),

            Stat::make('Best score', $bestScore ? number_format($bestScore, 1) . '/100' : '—')
                ->description('Personal best')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}
