<?php

namespace App\Filament\Student\Widgets;

use App\Models\QuizAttempt;
use Filament\Widgets\ChartWidget;

class MyScoreTrendChart extends ChartWidget
{
    protected static ?string $heading = 'My score trend — last 30 days';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();

        $attempts = QuizAttempt::query()
            ->where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $start)
            ->orderBy('submitted_at')
            ->get(['submitted_at', 'score']);

        $labels = [];
        $values = [];
        foreach ($attempts as $a) {
            $labels[] = $a->submitted_at->format('M j');
            $values[] = (float) $a->score;
        }

        if (empty($values)) {
            $labels[] = now()->format('M j');
            $values[] = 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Score',
                    'data'            => $values,
                    'borderColor'     => '#4154f1',
                    'backgroundColor' => 'rgba(65, 84, 241, 0.15)',
                    'fill'            => true,
                    'tension'         => 0.35,
                    'pointRadius'     => 4,
                    'pointHoverRadius'=> 7,
                    'borderWidth'     => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales'  => [
                'y' => ['beginAtZero' => true, 'max' => 100, 'ticks' => ['stepSize' => 20]],
            ],
        ];
    }
}
