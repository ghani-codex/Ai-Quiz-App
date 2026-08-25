<?php

namespace App\Filament\Teacher\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Teacher dashboard — single-column grid so every widget gets the
 * full row width. StatsOverview widgets still arrange their internal
 * Stat boxes in their own multi-column layout.
 */
class Dashboard extends BaseDashboard
{
    public function getColumns(): int|string|array
    {
        return 1;
    }
}
