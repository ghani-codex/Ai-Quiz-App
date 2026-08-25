<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopCoursesTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Top courses by enrollment';

    protected int|string|array $columnSpan = ['md' => 1, 'xl' => 1];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Course::query()
                    ->withCount('enrollments')
                    ->where('is_published', true)
                    ->orderByDesc('enrollments_count')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')->circular()->size(36),
                Tables\Columns\TextColumn::make('title')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('category.name')->badge(),
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Students')
                    ->badge()
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}
