<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use Filament\Widgets\ChartWidget;

class EnrollmentChart extends ChartWidget
{
    protected static ?string $heading = 'New enrollments — last 30 days';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();

        $rows = Enrollment::query()
            ->where('enrolled_at', '>=', $start)
            ->selectRaw('DATE(enrolled_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $labels  = [];
        $values  = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('M j');
            $values[] = (int) ($rows[$day->toDateString()] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Enrollments',
                    'data'                 => $values,
                    'borderColor'          => '#4154f1',
                    'backgroundColor'      => 'rgba(65, 84, 241, 0.12)',
                    'fill'                 => true,
                    'tension'              => 0.35,
                    'pointRadius'          => 0,
                    'pointHoverRadius'     => 5,
                    'borderWidth'          => 2,
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
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
