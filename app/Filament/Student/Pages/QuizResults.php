<?php

namespace App\Filament\Student\Pages;

use App\Models\QuizAttempt;
use Filament\Pages\Page;

class QuizResults extends Page
{
    protected static string $view = 'filament.student.pages.quiz-results';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Quiz Results';

    public QuizAttempt $attempt;

    public static function getSlug(): string
    {
        return 'attempts/{attempt}/results';
    }

    public function mount(QuizAttempt $attempt): void
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        $this->attempt = $attempt->load([
            'quiz.lecture.course',
            'quiz.questions.options',
            'answers.question',
            'answers.selectedOption',
        ]);
    }

    public function getScoreColor(): string
    {
        $s = (float) $this->attempt->score;
        if ($s >= 70) return 'success';
        if ($s >= 50) return 'warning';
        return 'danger';
    }

    public function getScoreVerdict(): string
    {
        $s = (float) $this->attempt->score;
        if ($s >= 90) return 'Outstanding';
        if ($s >= 70) return 'Well done';
        if ($s >= 50) return 'Solid — review the misses';
        return 'Time to revise — see explanations below';
    }
}
