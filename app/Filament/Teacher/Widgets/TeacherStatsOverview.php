<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $teacherId = auth()->id();

        $courses = Course::where('teacher_id', $teacherId);
        $coursesTotal     = (clone $courses)->count();
        $coursesPublished = (clone $courses)->where('is_published', true)->count();

        $students = \App\Models\Enrollment::query()
            ->whereHas('course', fn ($q) => $q->where('teacher_id', $teacherId))
            ->distinct('user_id');
        $studentsCount = $students->count('user_id');

        $myQuizIds = Quiz::whereHas('lecture.course', fn ($q) => $q->where('teacher_id', $teacherId))->pluck('id');
        $quizzesPublished = Quiz::whereIn('id', $myQuizIds)->where('is_published', true)->count();

        $attempts = QuizAttempt::whereIn('quiz_id', $myQuizIds);
        $attemptCount = (clone $attempts)->whereNotNull('submitted_at')->count();
        $avgScore     = (clone $attempts)->whereNotNull('score')->avg('score');

        return [
            Stat::make('My Courses', $coursesTotal)
                ->description($coursesPublished . ' published · ' . ($coursesTotal - $coursesPublished) . ' draft')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('My Students', $studentsCount)
                ->description('Enrolled across all my courses')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Published Quizzes', $quizzesPublished)
                ->description($attemptCount . ' attempts to date')
                ->descriptionIcon('heroicon-m-puzzle-piece')
                ->color('warning'),

            Stat::make('Average Score', $avgScore ? number_format($avgScore, 1) . '/100' : '—')
                ->description($attemptCount > 0 ? 'Across all submitted attempts' : 'No attempts yet')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgScore && $avgScore >= 70 ? 'success' : ($avgScore && $avgScore >= 50 ? 'warning' : 'gray')),
        ];
    }
}
